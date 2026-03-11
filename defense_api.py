from flask import Flask, request, jsonify
import joblib
import numpy as np
import time

app = Flask(__name__)

# Load models into memory once at startup for lowest possible latency
try:
    print("[*] Loading ML models into memory...")
    rf_model = joblib.load("ddos_detector_model.pkl")
    scaler = joblib.load("scaler.pkl")
    print("[+] Models loaded successfully.")
except Exception as e:
    print(f"[!] Error loading models: {e}")
    rf_model = None
    scaler = None

@app.route('/predict', methods=['POST'])
def predict():
    if not rf_model or not scaler:
        # Failsafe: if model is down, we allow to not disrupt business
        return jsonify({"status": "allow", "error": "Model not loaded"}), 500

    data = request.get_json()
    
    if not data:
        return jsonify({"error": "Invalid JSON"}), 400

    # Extract JSON fields based on the user's PHP payload
    ip = data.get('ip', '0.0.0.0')
    recent_requests_count = float(data.get('recent_requests_count', 0))
    avg_interval = float(data.get('avg_interval', 10.0))

    # To match the model's 4 features: 
    # ['count_request', 'avg_time_interval', 'distinct_url_count', 'std_dev_packet_size']
    # We map the inputs and provide safe defaults for the missing payload fields.
    # We use raw numpy lists instead of Pandas DataFrame to ensure microsecond latency.
    
    # Defaults: distinct_url_count=1 (Bots often hit 1 URL), std_dev_packet_size=0
    features = np.array([[recent_requests_count, avg_interval, 1.0, 0.0]])
    
    try:
        # Scale features
        features_scaled = scaler.transform(features)
        
        # Predict: returns array of labels, e.g., [1]
        prediction = rf_model.predict(features_scaled)
        
        if prediction[0] == 1:
            return jsonify({"status": "block"})
        else:
            return jsonify({"status": "allow"})
            
    except Exception as e:
        return jsonify({"status": "allow", "error": str(e)}), 500

if __name__ == '__main__':
    # Run heavily optimized for production-like responsiveness over localhost
    # Binds to port 5000 by default
    print("[*] Starting Defense API...")
    app.run(host='127.0.0.1', port=5000)
