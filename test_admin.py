import requests
import re
import urllib3

urllib3.disable_warnings()

url_login = 'https://jostru.site/login'
url_target = 'https://jostru.site/admin/waste-deposits'

session = requests.Session()

response = session.get(url_login, verify=False)
html = response.text

token_match = re.search(r'name="_token"\s+value="([^"]+)"', html)
if not token_match:
    print('No token')
    exit()
token = token_match.group(1)

data = {
    '_token': token,
    'email': 'plikocommunity@gmail.com',
    'password': 'Djafu12345@#'
}
res_login = session.post(url_login, data=data, verify=False)
print('Login:', res_login.status_code)

res_target = session.get(url_target, verify=False)
print('Target:', res_target.status_code)
if res_target.status_code == 500:
    print('500 ERROR!')
else:
    print('Length:', len(res_target.text))
    if 'page-transition-overlay' in res_target.text:
        print('Overlay is present')
