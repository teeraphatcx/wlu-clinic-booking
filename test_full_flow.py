import time
import re
import urllib.request
import urllib.parse
import http.cookiejar

cookie_jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))

# Login
with opener.open("http://localhost:8008/admin/login") as response:
    html = response.read().decode('utf-8')

token_match = re.search(r'name="_token"\s+value="([^"]+)"', html)
csrf_token = token_match.group(1)

login_data = urllib.parse.urlencode({
    "_token": csrf_token,
    "email": "admin@wlu-clinic.local",
    "password": "Admin@1234",
}).encode('utf-8')

req = urllib.request.Request("http://localhost:8008/admin/login", data=login_data)
opener.open(req)

pages = [
    "/admin/dashboard",
    "/admin/bookings",
    "/admin/services",
    "/admin/therapists",
    "/admin/schedules",
    "/admin/users",
    "/admin/contents",
    "/admin/reports",
    "/admin/settings",
    "/admin/admins",
]

print("Testing all admin pages:")
for page in pages:
    t0 = time.time()
    with opener.open(f"http://localhost:8008{page}") as res:
        elapsed = time.time() - t0
        print(f"  {page:25s} -> Status: {res.status} | URL: {res.url} | Time: {elapsed:.3f}s")
