import requests
import threading
import time
import random
import csv
from datetime import datetime

# ================= CẤU HÌNH =================
TARGET_URL = "http://localhost/E-commerce%20Volley%20(1)" # Đổi thành URL web PHP thật của bạn nếu cần
LOG_FILE = "access_log.csv"

# Khóa luồng để tránh đụng độ khi ghi file CSV từ đa luồng
LOCK = threading.Lock()

# Danh sách User-Agents đa dạng
NORMAL_USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Safari/605.1.15",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5735.196 Mobile Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0"
]

BOT_USER_AGENTS = [
    "Bot/1.0",
    "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
    "curl/7.68.0"
]

# Các endpoint mô phỏng
NORMAL_ENDPOINTS = ["/home", "/products", "/cart"]
ATTACK_ENDPOINT = "/search?q="

def write_log(ip_address, timestamp, request_method, url_accessed, user_agent, response_size, label):
    """Hàm an toàn luồng để ghi dữ liệu log ra file CSV."""
    with LOCK:
        with open(LOG_FILE, mode='a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            writer.writerow([ip_address, timestamp, request_method, url_accessed, user_agent, response_size, label])

def normal_user(user_id):
    """Mô phỏng thực thể người dùng bình thường."""
    # Mỗi user sẽ lấy 1 IP cố định ảo, ví dụ 192.168.1.100 -> 104
    ip_address = f"192.168.1.{100 + user_id}"
    user_agent = random.choice(NORMAL_USER_AGENTS)
    
    print(f"[*] Đã khởi động Normal User {user_id} (IP: {ip_address})")
    
    while True:
        endpoint = random.choice(NORMAL_ENDPOINTS)
        url = TARGET_URL + endpoint
        
        # Gửi thêm X-Forwarded-For để PHP backend có thể ghi nhận IP ảo nếu muốn
        headers = {"User-Agent": user_agent, "X-Forwarded-For": ip_address}
        
        try:
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            # Tạo delay giả cho thao tác gửi request để lấy response
            response = requests.get(url, headers=headers, timeout=5)
            response_size = len(response.content)
            
            # Ghi log: Label là 0 (Normal)
            write_log(ip_address, timestamp, "GET", endpoint, user_agent, response_size, 0)
        except requests.RequestException:
            # Nếu Request thất bại (timeout/ sập server), response_size = 0
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            write_log(ip_address, timestamp, "GET", endpoint, user_agent, 0, 0)
            
        # Thời gian nghỉ từ 2-5 giây giữa các thao tác
        time.sleep(random.randint(2, 5))

def ddos_bot(bot_id):
    """Mô phỏng thực thể Bot DDoS."""
    # Các bot sử dụng dải IP nội bộ khác biệt
    ip_address = f"10.0.0.{50 + bot_id}"
    user_agent = random.choice(BOT_USER_AGENTS)
    
    print(f"[!] Đã khởi động DDoS Bot {bot_id} (IP: {ip_address})")
    
    while True:
        # random chuỗi payload để tránh bypass caching tại web server PHP
        query_string = f"payload_{random.randint(1, 999999)}"
        endpoint = ATTACK_ENDPOINT + query_string
        url = TARGET_URL + endpoint
        
        headers = {"User-Agent": user_agent, "X-Forwarded-For": ip_address}
        
        try:
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            response = requests.get(url, headers=headers, timeout=3)
            response_size = len(response.content)
            
            # Ghi log: Label là 1 (Attack)
            write_log(ip_address, timestamp, "GET", endpoint, user_agent, response_size, 1)
        except requests.RequestException:
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            write_log(ip_address, timestamp, "GET", endpoint, user_agent, 0, 1)
            
        # KHÔNG có sleep => Request gửi liên tục

if __name__ == "__main__":
    # 1. Khởi tạo file log và ghi các tiêu đề cột (Header)
    with open(LOG_FILE, mode='w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(["ip_address", "timestamp", "request_method", "url_accessed", "user_agent", "response_size", "label"])

    print("--- Bắt đầu mô phỏng Network Traffic ---")
    threads = []
    
    # 2. Khởi tạo Kịch bản 1: 5 Normal Users
    for i in range(5):
        t = threading.Thread(target=normal_user, args=(i,))
        t.daemon = True # Biến luồng thành kiểu daemon để ngắt nhanh khi tắt console
        threads.append(t)
        t.start()
        
    # Chờ nhẹ khoảng 2 giây để traffic normal chạy ổn định
    time.sleep(2)
    
    # 3. Khởi tạo Kịch bản 2: 2 DDoS Bots
    for i in range(2):
        t = threading.Thread(target=ddos_bot, args=(i,))
        t.daemon = True
        threads.append(t)
        t.start()

    # 4. Giữ hàm main chờ vô hạn. Bấm Ctrl+C trên terminal để dừng lại.
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n--- Đã dừng mô phỏng. Hãy kiểm tra file access_log.csv ---")
