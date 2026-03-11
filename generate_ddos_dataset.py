import requests
import threading
import time
import random
import csv
from datetime import datetime

# Configuration
BASE_URL = "http://localhost:3000"
CSV_FILE = "access_log.csv"
DURATION_SECONDS = 30  # Let it run for 30 seconds for test output, can be changed by user

# Endpoints
NORMAL_ENDPOINTS = [
    "/home",
    "/products",
    "/cart"
]
DDOS_ENDPOINT = "/search?query=test"

# User Agents
USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Safari/605.1.15",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (Linux; Android 13; SM-S901B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0"
]

# Write lock for CSV concurrency
csv_lock = threading.Lock()

# Global flag to stop threads cleanly
stop_flag = False

def init_csv():
    """ Initialize CSV structure with headers """
    with open(CSV_FILE, mode='w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(["ip_address", "timestamp", "request_method", "url_accessed", "user_agent", "response_size", "label"])
    print(f"[*] Initialized {CSV_FILE}")

def log_request(ip_address, method, url, user_agent, response_size, label):
    """ Thread-safe writing to CSV file """
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with csv_lock:
        with open(CSV_FILE, mode='a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            writer.writerow([ip_address, timestamp, method, url, user_agent, response_size, label])

def generate_massive_dataset(total_rows=10000):
    """ Generates a large dataset completely offline without burdening the PHP server. """
    print(f"[*] Starting offline massive data generation targeting {total_rows} rows...")
    
    # Attack Targets
    attack_targets = [
        "/search?query=test",
        "/products",
        "/detail.php?id=1",
        "/login",
        "/register",
        "/categories",
        "/category?id=2",
        "/cart",
        "/cart-add",
        "/checkout",
        "/contact-submit",
        "/admin-dashboard",
        "/seller-dashboard"
    ]
    
    bot_ips = ["10.0.0.201", "10.0.0.202"]
    bot_ua = "Python-urllib/3.9"
    
    normal_ips = [f"192.168.1.{100+i}" for i in range(1, 10)]
    
    current_time = datetime.now()
    
    # We will generate blocks of time to simulate a real continuous timeline
    rows_generated = 0
    
    with open(CSV_FILE, mode='a', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        
        while rows_generated < total_rows:
            # Decisions for this current second
            # 80% chance it's a normal second, 20% chance the bots unleash hell
            if random.random() < 0.2:
                # DDoS Burst (Bots firing 50-100 requests in the exact same second)
                burst_size = random.randint(50, 100)
                for _ in range(burst_size):
                    ip = random.choice(bot_ips)
                    url = random.choice(attack_targets)
                    size = random.randint(100, 500)
                    ts = current_time.strftime("%Y-%m-%d %H:%M:%S")
                    writer.writerow([ip, ts, "GET", url, bot_ua, size, 1])
                    rows_generated += 1
                    if rows_generated >= total_rows: break
            else:
                # Normal Traffic (Users browsing slowly 1-3 requests per second)
                reqs = random.randint(1, 4)
                for _ in range(reqs):
                    ip = random.choice(normal_ips)
                    url = random.choice(NORMAL_ENDPOINTS)
                    ua = random.choice(USER_AGENTS)
                    size = random.randint(1000, 50000)
                    ts = current_time.strftime("%Y-%m-%d %H:%M:%S")
                    writer.writerow([ip, ts, "GET", url, ua, size, 0])
                    rows_generated += 1
                    if rows_generated >= total_rows: break
                    
            # Advance time by 1 second
            from datetime import timedelta
            current_time += timedelta(seconds=1)
            
            if rows_generated % 2000 == 0 and rows_generated > 0:
                print(f"[*] Generated {rows_generated} rows...")
                
    print(f"[+] Complete! Generated {rows_generated} rows in {CSV_FILE}")

if __name__ == "__main__":
    init_csv()
    generate_massive_dataset(15000) # Give them a solid 15k rows
