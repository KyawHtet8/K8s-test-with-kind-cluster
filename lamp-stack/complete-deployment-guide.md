# 🎓 Professor's Complete LAMP Stack Deployment Guide
## Myanmar Students အတွက် Step-by-Step လမ်းညွှန်

---

## 📋 Pre-requisites (ကြိုတင်လိုအပ်ချက်များ)

### 1. Environment Setup
```bash
# Check kubectl installation
kubectl version --client

# Check cluster connection  
kubectl cluster-info

# Check available resources
kubectl get nodes
```

### 2. Create Project Directory
```bash
# Create learning directory
mkdir lamp-learning
cd lamp-learning
```

---

## 🚀 Step 1: Deploy the Perfect LAMP Stack

### Save the YAML File
```bash
# Save the complete YAML as lamp-stack-perfect.yaml
cat > lamp-stack-perfect.yaml << 'EOF'
# [Paste the complete YAML from the first artifact here]
EOF
```

### Deploy to Kubernetes
```bash
# Apply all resources
kubectl apply -f lamp-stack-perfect.yaml

# Watch the deployment
kubectl get pods -l app=lamp-app -w
```

### Verify Resource Creation
```bash
# Check all resources
kubectl get all -l app=lamp-stack

# Check specific resources
kubectl get configmap php-config
kubectl get secret mysql-secrets
kubectl get deployment lamp-wp
kubectl get service
kubectl get hpa lamp-hpa
```

---

## 🔍 Step 2: Monitor Deployment Progress

### Check Pod Status
```bash
# Get pod details
kubectl describe pod -l app=lamp-app

# Check container logs
POD_NAME=$(kubectl get pods -l app=lamp-app -o jsonpath='{.items[0].metadata.name}')
kubectl logs $POD_NAME -c httpd-php-container
kubectl logs $POD_NAME -c mysql-container
```

### Wait for Full Initialization
```bash
# MySQL takes 30-60 seconds to fully initialize
# Watch for this message in MySQL logs: "mysqld: ready for connections"
kubectl logs $POD_NAME -c mysql-container | grep "ready for connections"
```

---

## 📁 Step 3: Deploy the Perfect PHP Application

### Create the PHP File Locally
```bash
# Save the perfect index.php file
cat > index.php << 'EOF'
# [Paste the complete PHP code from the second artifact here]
EOF
```

### Copy to Container
```bash
# Copy PHP file to the Apache document root
kubectl cp index.php $POD_NAME:/app/index.php -c httpd-php-container

# Verify file exists and has correct content
kubectl exec -it $POD_NAME -c httpd-php-container -- ls -la /app/
kubectl exec -it $POD_NAME -c httpd-php-container -- head -20 /app/index.php
```

---

## 🧪 Step 4: Test the Application

### Internal Testing (From Pod)
```bash
# Test HTTP response from within pod
kubectl exec -it $POD_NAME -c httpd-php-container -- curl -s http://localhost/index.php | head -50

# Check if "Connected successfully" appears
kubectl exec -it $POD_NAME -c httpd-php-container -- curl -s http://localhost/index.php | grep "Connected successfully"
```

### External Testing

#### Method 1: Port Forward (Recommended for Learning)
```bash
# Terminal 1: Start port forwarding
kubectl port-forward svc/lamp-service 8080:80

# Terminal 2: Test the application
curl http://localhost:8080/index.php | head -50
# Or open in browser: http://localhost:8080/index.php
```

#### Method 2: NodePort (Production-like)
```bash
# Get node information
kubectl get nodes -o wide

# For Minikube users
minikube ip

# Test with node IP (replace with actual IP)
curl http://<NODE_IP>:30008/index.php

# For KodeKloud or cloud environments, use provided URL
curl http://30008-port-xxxxx.labs.kodekloud.com/index.php
```

---

## 🔧 Step 5: Advanced Testing & Validation

### Database Connection Test
```bash
# Connect to MySQL from MySQL container
kubectl exec -it $POD_NAME -c mysql-container -- mysql -u lamp_user -plamp_password_123 -h localhost lamp_database -e "SHOW TABLES;"

# Check if sample data was created
kubectl exec -it $POD_NAME -c mysql-container -- mysql -u lamp_user -plamp_password_123 -h localhost lamp_database -e "SELECT COUNT(*) FROM students;"
```

### Environment Variables Verification
```bash
# Check all environment variables in PHP container
kubectl exec -it $POD_NAME -c httpd-php-container -- env | grep MYSQL

# Should show:
# MYSQL_HOST=localhost
# MYSQL_DATABASE=lamp_database  
# MYSQL_USER=lamp_user