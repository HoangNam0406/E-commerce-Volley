# Hướng dẫn chạy và kiểm thử Hệ thống AI chống DDoS

Chào bạn, đây là hướng dẫn chi tiết từng bước để bạn có thể tự mình khởi động toàn bộ hệ thống phòng thủ DDoS bằng Trí tuệ Nhân tạo (Machine Learning) cho website E-commerce của bạn.

Hệ thống hoạt động theo 2 thành phần độc lập nhưng song song với nhau:
1. **PHP Web Server:** Phục vụ người dùng (như bình thường).
2. **Python Defense API:** Chạy ngầm để nhận diện hành vi tấn công từ PHP gửi sang.

Dưới đây là cách bạn chạy và kiểm thử hệ thống. Mở một vài cửa sổ **Terminal/Command Prompt** (hoặc dùng terminal tích hợp trong VSCode) tại thư mục dự án `E-commerce Volley (1)` để thực hiện nhé.

---

## BƯỚC 1: Rèn luyện bộ não AI (Chỉ cần làm 1 lần hoặc khi cần model mới)

Nếu bạn chưa có file `ddos_detector_model.pkl`, bạn cần làm bước này. Mình đã chạy giúp bạn rồi nên bạn có thể **Bỏ qua** bước này nếu muốn chạy luôn web.

*   Mở Terminal số 1, chạy lệnh để tạo data (giả lập 30 giây bot vs người thật):
    ```sh
    py generate_ddos_dataset.py
    ```
    *(Sẽ tạo ra file `access_log.csv`)*
*   Sau khi chạy xong, tiến hành train AI:
    ```sh
    py train_ddos_model.py
    ```
    *(Sẽ in ra độ chính xác 100% và tạo ra file `ddos_detector_model.pkl`)*

---

## BƯỚC 2: Bật "Trạm gác" AI (Defense API)

API này **BẮT BUỘC PHẢI CHẠY LIÊN TỤC** thì web PHP mới có thể hỏi ý kiến nó được.

*   Mở Terminal số 2, chạy lệnh khởi động Server AI (Port 5000):
    ```sh
    py defense_api.py
    ```
*   Nếu Màn hình hiện dòng chữ `* Running on http://127.0.0.1:5000` là thành công! **Cứ treo cái cửa sổ đen này ở đó, đừng tắt nhé.**

---

## BƯỚC 3: Bật Website PHP (Nếu web chưa chạy)

Bạn cần bật tính năng theo dõi IP trong file `config.php` (nếu cần đổi cổng) hoặc chỉ cần dùng cổng 3000 như bạn thiết lập.

*   Mở Terminal số 3, bật PHP Web bằng lệnh:
    ```sh
    php -S localhost:3000 -t public
    ```
*   *(Hoặc nếu bạn dùng XAMPP Apache thì bật Apache lên và truy cập như bình thường, vì code mình viết đã tự gắn vào `index.php` gốc rồi).*

---

## BƯỚC 4: Kiểm thử Bắn DDoS để xem AI hoạt động

Bạn đang đóng vai là một Hacker. Bạn viết một đoạn kịch bản bắn traffic liên tục vào web.

*   Mở Terminal số 4, chạy lệnh:
    ```sh
    py test_integration.py
    ```
*   **Quan sát kết quả:** Bạn sẽ thấy lệnh spam Python gửi request GET liên tục vào `http://localhost:3000/home`.
*   Khoảng 15-18 request đầu tiên: Web vẫn trả về `HTTP 200 (OK)` cho phép vào.
*   Nhưng ngay khi Web gửi thống kê sang **Terminal số 2 (Trạm gác AI)**, AI sẽ nhận thấy đặc điểm: "Tên này request liên tục với avg_time_error = 0.05s, không có thời gian nghỉ, giống hệt dữ liệu DDoS!". 
*   AI sẽ báo cho Web PHP chặn. 
*   Lập tức các Request tiếp theo của Hacker (Terminal số 4) sẽ bị đá văng với mã lỗi `HTTP 403 Blocked. Hệ thống phát hiện tấn công từ IP của bạn!`.

---

## Nếu bị khóa lỡ rồi, làm sao để vào lại web?

Vì đây là bản Demo dùng Session nên việc mở khóa rất đơn giản:
1. Bạn vào thẻ tab ẩn danh lưu trên trình duyệt (hoặc clear cookies/session).
2. Hoặc chờ quá 60 giây, Session history của IP bạn sẽ tự động được làm mới lại.

## Làm sao để so sánh AI (ML-based) và Cách chặn truyền thống (Rule-based)?

Bạn hãy mở file `core/SecurityMiddleware.php`.
Ở dòng 14, bạn sẽ thấy thông số này:
```php
const USE_MACHINE_LEARNING = true;
```

*   Đổi thành `false` và lưu lại -> Web sẽ bỏ qua Trạm gác AI và tự xét nét (>20 request/phút thì chặn). Hãy thử dùng `py test_integration.py` lại để đo xem cách nào nhanh và chính xác hơn cho phần Báo cáo đề tài nhé!

Chúc đề tài đồ án của bạn đạt điểm tối đa! Tụi mình đã xây dựng được một kiến trúc Microservice AI Phòng thủ cực kì hiện đại đấy.
