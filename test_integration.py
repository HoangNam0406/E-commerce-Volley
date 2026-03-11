import requests
import time
import sys

# Target the actual PHP web server URL
URL = "http://localhost:3000/home"

print(f"[*] Starting attack on {URL}...")
print("[*] Note: Since the Defense API tracks IPs using PHP Sessions, this script maintains a single requests Session.")

session = requests.Session()

# Spam requests
for i in range(1, 31):
    try:
        start_time = time.time()
        response = session.get(URL, timeout=3)
        elapsed = time.time() - start_time
        
        status = response.status_code
        if status == 403:
            print(f"[!] Request {i:02d}: Blocked (HTTP 403).")
            print("[+] Defense System Working! AI successfully blocked the flood request.")
            sys.exit(0)
        else:
            print(f"[*] Request {i:02d}: Allowed (HTTP {status}) in {elapsed:.3f}s")
            
        # No sleep to simulate flood
    except Exception as e:
        print(f"[!] Error: {e}")

print("[-] Attack finished. But we weren't blocked? (Maybe API is down or feature threshold not met)")
