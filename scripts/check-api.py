#!/usr/bin/env python3
"""Check real HTTP responses against the disposable CI database, never production."""
import json
import os
from pathlib import Path
import shutil
import socket
import subprocess
import tempfile
import time
import urllib.error
import urllib.request

root = Path(__file__).resolve().parents[1]
assert os.environ.get('TINY_LLM_TEST_DB_HOST'), 'Configure the disposable test database first.'
checks = 0
with tempfile.TemporaryDirectory(prefix='tiny-api-') as directory:
    site = Path(directory)
    for folder in ('app', 'core', 'routes'):
        shutil.copytree(root / folder, site / folder)
    for filename in ('index.php', 'config.php'):
        shutil.copy2(root / filename, site / filename)
    (site / 'config.local.php').write_text('''<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(getenv('TINY_LLM_TEST_DB_HOST'), getenv('TINY_LLM_TEST_DB_USER'),
    getenv('TINY_LLM_TEST_DB_PASSWORD'), getenv('TINY_LLM_TEST_DB_NAME'),
    (int) getenv('TINY_LLM_TEST_DB_PORT'));
$db->set_charset('utf8mb4');
''')
    (site / 'test-router.php').write_text("<?php\n$_SERVER['SCRIPT_NAME'] = str_starts_with($_SERVER['REQUEST_URI'], '/tiny-llm-web/') ? '/tiny-llm-web/index.php' : '/index.php';\nrequire __DIR__ . '/index.php';\n")
    with socket.socket() as sock:
        sock.bind(('127.0.0.1', 0))
        port = sock.getsockname()[1]
    with tempfile.TemporaryFile() as log:
        server = subprocess.Popen(['php', '-d', 'display_errors=1', '-S', f'127.0.0.1:{port}',
            'test-router.php'], cwd=site, stdout=log, stderr=log)
        try:
            for _ in range(100):
                try:
                    with socket.create_connection(('127.0.0.1', port), timeout=0.1):
                        break
                except OSError:
                    if server.poll() is not None:
                        raise RuntimeError('PHP test server failed to start')
                    time.sleep(0.05)
            else:
                raise RuntimeError('PHP test server did not become ready')

            def request(path, status=200, method='GET'):
                global checks
                req = urllib.request.Request(f'http://127.0.0.1:{port}' + path, method=method)
                try:
                    response = urllib.request.urlopen(req, timeout=10)
                except urllib.error.HTTPError as error:
                    response = error
                with response:
                    assert response.status == status, (path, response.status, status)
                    assert response.headers.get_content_type() == 'application/json', path
                    payload = json.loads(response.read())
                    if status == 405:
                        assert response.headers['Allow'] == 'GET'
                checks += 1
                return payload

            for resource, key, count in [('machines', 'machine_id', 2), ('models', 'model_id', 2),
                    ('datasets', 'dataset_id', 2), ('runs', 'run_id', 6)]:
                path = '/api/v1/' + resource
                listing = request(path)
                assert listing['count'] == count == len(listing['data']), path
                assert int(request(path + '/1')['data'][key]) == 1
                for bad_id in ['999999', '0', '-1', 'abc', '1.2', '1e3',
                        '9223372036854775808', '18446744073709551615']:
                    assert 'error' in request(path + '/' + bad_id, 404)
                request(path, 405, 'POST')
                request(path + '/1', 405, 'DELETE')
            runs = request('/api/v1/machines/1/runs')
            assert runs['machine_id'] == 1 and runs['hostname'] == 'test1'
            assert runs['count'] == 2 == len(runs['data'])
            assert [int(row['run_id']) for row in runs['data']] == [2, 1]
            request('/api/v1/machines/999999/runs', 404)
            request('/api/v1/machines/18446744073709551615/runs', 404)
            request('/api/v1/machines/1/runs', 405, 'POST')
            request('/api/v1/missing', 404)
            request('/api/v1/machines/1/extra', 404)
            # Base-path deployment uses SCRIPT_NAME to strip the URL prefix.
            request('/tiny-llm-web/api/v1/machines')
            # Transactional deletes roll back when the request's connection closes.
            config = site / 'config.local.php'
            original_config = config.read_text()
            config.write_text(original_config + "\n$db->begin_transaction();\n$db->query('DELETE FROM benchmark_runs');\n")
            assert request('/api/v1/runs') == {'data': [], 'count': 0}
            empty = request('/api/v1/machines/1/runs')
            assert empty['data'] == [] and empty['count'] == 0 and empty['machine_id'] == 1
            config.write_text("<?php throw new RuntimeException('secret database credentials');\n")
            assert request('/api/v1/machines', 500) == {'error': 'Internal server error'}
            # Missing local configuration must remain a JSON 500 even with display_errors enabled.
            (site / 'config.local.php').unlink()
            assert request('/api/v1/machines', 500) == {'error': 'Internal server error'}
            print(f'{checks} API HTTP checks passed.')
        finally:
            server.terminate()
            server.wait(timeout=5)
