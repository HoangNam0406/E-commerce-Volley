import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score, precision_score, recall_score
import joblib

# Configuration
INPUT_CSV = "access_log.csv"
MODEL_FILE = "ddos_detector_model.pkl"
SCALER_FILE = "scaler.pkl"

def preprocess_and_extract_features(df):
    """ Extract features based on 10-second rolling window per IP address """
    print("[*] Preprocessing and extracting features...")
    
    # Ensure timestamp is datetime type
    df['timestamp'] = pd.to_datetime(df['timestamp'])
    
    # Sort by IP and timestamp for proper rolling window operations
    df = df.sort_values(by=['ip_address', 'timestamp'])
    
    # Set timestamp as index for resample/rolling operations
    df.set_index('timestamp', inplace=True)
    
    # Group by IP and a 10-second window
    grouped = df.groupby(['ip_address', pd.Grouper(freq='10s')])
    
    features = []
    
    for (ip, timestamp_window), group in grouped:
        if group.empty:
            continue
            
        # Feature 1: count_request
        count_request = len(group)
        
        # Feature 2: avg_time_interval
        if count_request > 1:
            time_diffs = group.index.to_series().diff().dt.total_seconds().dropna()
            avg_time_interval = time_diffs.mean()
        else:
            avg_time_interval = 10.0 # Default if only 1 request
            
        # Feature 3: distinct_url_count
        distinct_url_count = group['url_accessed'].nunique()
        
        # Feature 4: std_dev_packet_size
        if count_request > 1:
            std_dev_packet_size = group['response_size'].std()
            if pd.isna(std_dev_packet_size):
                std_dev_packet_size = 0.0
        else:
            std_dev_packet_size = 0.0
            
        # Label: if any request in the window is marked as attack, log the window as attack
        label = group['label'].max()
        
        features.append({
            'ip_address': ip,
            'window_start': timestamp_window,
            'count_request': count_request,
            'avg_time_interval': avg_time_interval,
            'distinct_url_count': distinct_url_count,
            'std_dev_packet_size': std_dev_packet_size,
            'label': label
        })
        
    features_df = pd.DataFrame(features)
    return features_df

def main():
    print(f"[*] Loading dataset: {INPUT_CSV}")
    try:
        df = pd.read_csv(INPUT_CSV)
    except FileNotFoundError:
        print(f"[!] File {INPUT_CSV} not found. Please run generate_ddos_dataset.py first.")
        return

    features_df = preprocess_and_extract_features(df)
    
    print(f"[*] Extracted {len(features_df)} sample windows.")
    print(features_df['label'].value_counts())
    
    # Define X and y
    X = features_df[['count_request', 'avg_time_interval', 'distinct_url_count', 'std_dev_packet_size']]
    y = features_df['label']
    
    # Train-test split (80/20)
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
    
    # Scale Features
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)
    
    # Initialize and Train Random Forest
    rf_model = RandomForestClassifier(n_estimators=100, random_state=42)
    print("\n[*] Training Random Forest model...")
    rf_model.fit(X_train_scaled, y_train)
    
    # Predict and Evaluate
    y_pred = rf_model.predict(X_test_scaled)
    
    print("\n--- Model Evaluation ---")
    
    cm = confusion_matrix(y_test, y_pred)
    acc = accuracy_score(y_test, y_pred)
    prec = precision_score(y_test, y_pred, zero_division=0)
    rec = recall_score(y_test, y_pred, zero_division=0)
    
    print(f"Accuracy:  {acc:.4f}")
    print(f"Precision: {prec:.4f}")
    print(f"Recall:    {rec:.4f}")
    
    print("\nConfusion Matrix:")
    print(pd.DataFrame(cm, columns=['Pred Normal(0)', 'Pred Attack(1)'], index=['True Normal(0)', 'True Attack(1)']))
    
    print("\nClassification Report:")
    print(classification_report(y_test, y_pred, target_names=['Normal(0)', 'Attack(1)']))
    
    # Export Models
    print("\n[*] Exporting models...")
    joblib.dump(rf_model, MODEL_FILE)
    print(f"[+] Saved model to {MODEL_FILE}")
    
    joblib.dump(scaler, SCALER_FILE)
    print(f"[+] Saved scaler to {SCALER_FILE}")
    
if __name__ == "__main__":
    main()
