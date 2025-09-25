****Kubernetes ပေါ်တွင် LEMP Stack အသုံးပြု၍ Microservices-based Web Application တစ်ခု တည်ဆောက်ခြင်းဆိုင်ရာ လေ့လာဆန်းစစ်ချက်****

This problem was base KodeCloud K8s level 3 exercise No.7

**နိဒါန်း**

ခေတ်မီ web application များ တည်ဆောက်ရာတွင် Monolithic ဗိသုကာပုံစံမှ Microservices ဗိသုကာပုံစံသို့ ပြောင်းလဲလာမှုကြောင့် စီမံခန့်ခွဲမှု လွယ်ကူခြင်း၊ scalability ကောင်းမွန်ခြင်းနှင့် resilience မြင့်မားခြင်း စသည့် အကျိုးကျေးဇူးများ ရရှိစေပါသည်။ ဤစာတမ်းတွင် Kubernetes orchestration engine ကို အသုံးပြု၍ LEMP (Linux, Nginx, MySQL, PHP-FPM) stack ဖြင့် ဝဘ်ဆိုဒ်တစ်ခုကို container deployment ပြုလုပ်ပုံအား အသေးစိတ် လေ့လာဆန်းစစ်တင်ပြထားပါသည်။

**Deployment အတွက် လေ့လာတွေ့ရှိချက်များ**

LEMP stack အား Kubernetes ပေါ်တွင် deploy လုပ်ရာ၌ Microservices စနစ်၏ အခြေခံမူများဖြစ်သော separation of concerns နှင့် service discovery ကို အလေးထား အကောင်အထည်ဖော်ရန်လိုအပ်ပါသည်။ ဤ deployment strategy တွင် အဓိက အစိတ်အပိုင်း (၂) ခု ပါဝင်ပါသည်။

၁။ Web Tier (Nginx & PHP-FPM) : HTTP requests များကို လက်ခံပြီး PHP script များအား လုပ်ဆောင်ပေးသည့် အပိုင်းဖြစ်သည်။
၂။ Database Tier (MySQL) : application အတွက် data များ သိမ်းဆည်းပေးသည့် အပိုင်းဖြစ်သည်။

ဤ Tier နှစ်ခုကို သီးခြားစီ ခွဲခြား၍ deploy လုပ်ခြင်းဖြင့် scalability နှင့် resilience ပိုမိုကောင်းမွန်စေပါသည်။

အကောင်အထည်ဖော်မည့် အဆင့်များ
LEMP stack ကို အောင်မြင်စွာ deploy လုပ်နိုင်ရန် အောက်ပါအဆင့်များကို စနစ်တကျ လုပ်ဆောင်ရန်လိုအပ်ပါသည်။

Secret နှင့် ConfigMap များ ဖန်တီးခြင်း: MySQL database credentials များကို Secrets များအဖြစ် hard-code မလုပ်ဘဲ သိမ်းဆည်းထားပြီး၊ php.ini နှင့် index.php ကဲ့သို့သော configuration ဖိုင်များကို ConfigMaps များဖြင့် စီမံခန့်ခွဲပါသည်။ ၎င်းသည် configuration ပြောင်းလဲမှုကို လွယ်ကူစေပြီး sensitive data ကိုလည်း လုံခြုံစေပါသည်။

Deployment နှင့် Container Image ရွေးချယ်မှု:

Nginx နှင့် PHP-FPM တို့ကို ပေါင်းစည်းထားသော webdevops/php-nginx:alpine-3-php7 image ကို အသုံးပြုပါသည်။

MySQL database အတွက် mysql:5.6 image ကို အသုံးပြုပါသည်။

Service Discovery အတွက် Service များ ဖန်တီးခြင်း:

lemp-service: web application ကို NodePort type ဖြင့် expose လုပ်ရန် ဖန်တီးပါသည်။

mysql-service: lemp-wp Deployment အတွင်းရှိ container များက MySQL ကို ၎င်း၏ Service name (ဥပမာ- mysql-service) ဖြင့် အလွယ်တကူ ချိတ်ဆက်နိုင်ရန် ဖန်တီးပါသည်။

အကောင်အထည်ဖော်မှု (Implementation)
ဤ strategy ကို အခြေခံ၍ လိုအပ်သော Kubernetes manifests များကို အောက်ပါအတိုင်း ဖန်တီးရေးသားထားပါသည်။

**Step 1 **

. Secrets and ConfigMaps
# Secrets
kubectl create secret generic mysql-root-pass --from-literal=password=R00t
kubectl create secret generic mysql-user-pass --from-literal=username=kodekloud_gem --from-literal=password=8FmzjvFU6S
kubectl create secret generic mysql-db-url --from-literal=database=kodekloud_db2
kubectl create secret generic mysql-host --from-literal=host=mysql-service
# ConfigMaps
kubectl create configmap php-config --from-literal=php.ini='variables_order = "EGPCS"'
kubectl create configmap index-file-configmap --from-literal=index.php='<?php $servername = getenv("MYSQL_HOST"); $username = getenv("MYSQL_USER"); $password = getenv("MYSQL_PASSWORD"); $dbname = getenv("MYSQL_DATABASE"); $connect = mysqli_connect($servername, $username, $password, $dbname); if (mysqli_connect_error()) { die("Connection failed: " . mysqli_connect_error()); } echo "Connected successfully"; ?>'

2. Deployment YAML (lemp-deployment.yaml)

apiVersion: apps/v1
kind: Deployment
metadata:
  name: lemp-wp
spec:
  replicas: 1
  selector:
    matchLabels:
      app: lemp-wp-app
  template:
    metadata:
      labels:
        app: lemp-wp-app
    spec:
      containers:
      - name: nginx-php-container
        image: webdevops/php-nginx:alpine-3-php7
        ports:
        - containerPort: 80
        volumeMounts:
        - name: php-config-volume
          mountPath: /opt/docker/etc/php/php.ini
          subPath: php.ini
        - name: index-file-volume
          mountPath: /app/index.php
          subPath: index.php
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      - name: mysql-container
        image: mysql:5.6
        ports:
        - containerPort: 3306
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      volumes:
      - name: php-config-volume
        configMap:
          name: php-config
      - name: index-file-volume
        configMap:
          name: index-file-configmap

ခေါင်းစဉ်။ Kubernetes ပေါ်တွင် LEMP Stack အသုံးပြု၍ Microservices-based Web Application တစ်ခု တည်ဆောက်ခြင်းဆိုင်ရာ လေ့လာဆန်းစစ်ချက်
နိဒါန်း
ခေတ်မီ web application များ တည်ဆောက်ရာတွင် Monolithic ဗိသုကာပုံစံမှ Microservices ဗိသုကာပုံစံသို့ ပြောင်းလဲလာမှုကြောင့် စီမံခန့်ခွဲမှု လွယ်ကူခြင်း၊ scalability ကောင်းမွန်ခြင်းနှင့် resilience မြင့်မားခြင်း စသည့် အကျိုးကျေးဇူးများ ရရှိစေပါသည်။ ဤစာတမ်းတွင် Kubernetes orchestration engine ကို အသုံးပြု၍ LEMP (Linux, Nginx, MySQL, PHP-FPM) stack ဖြင့် ဝဘ်ဆိုဒ်တစ်ခုကို container deployment ပြုလုပ်ပုံအား အသေးစိတ် လေ့လာဆန်းစစ်တင်ပြထားပါသည်။

Deployment အတွက် လေ့လာတွေ့ရှိချက်များ
LEMP stack အား Kubernetes ပေါ်တွင် deploy လုပ်ရာ၌ Microservices စနစ်၏ အခြေခံမူများဖြစ်သော separation of concerns နှင့် service discovery ကို အလေးထား အကောင်အထည်ဖော်ရန်လိုအပ်ပါသည်။ ဤ deployment strategy တွင် အဓိက အစိတ်အပိုင်း (၂) ခု ပါဝင်ပါသည်။

၁။ Web Tier (Nginx & PHP-FPM) : HTTP requests များကို လက်ခံပြီး PHP script များအား လုပ်ဆောင်ပေးသည့် အပိုင်းဖြစ်သည်။
၂။ Database Tier (MySQL) : application အတွက် data များ သိမ်းဆည်းပေးသည့် အပိုင်းဖြစ်သည်။

ဤ Tier နှစ်ခုကို သီးခြားစီ ခွဲခြား၍ deploy လုပ်ခြင်းဖြင့် scalability နှင့် resilience ပိုမိုကောင်းမွန်စေပါသည်။

အကောင်အထည်ဖော်မည့် အဆင့်များ
LEMP stack ကို အောင်မြင်စွာ deploy လုပ်နိုင်ရန် အောက်ပါအဆင့်များကို စနစ်တကျ လုပ်ဆောင်ရန်လိုအပ်ပါသည်။

Secret နှင့် ConfigMap များ ဖန်တီးခြင်း: MySQL database credentials များကို Secrets များအဖြစ် hard-code မလုပ်ဘဲ သိမ်းဆည်းထားပြီး၊ php.ini နှင့် index.php ကဲ့သို့သော configuration ဖိုင်များကို ConfigMaps များဖြင့် စီမံခန့်ခွဲပါသည်။ ၎င်းသည် configuration ပြောင်းလဲမှုကို လွယ်ကူစေပြီး sensitive data ကိုလည်း လုံခြုံစေပါသည်။

Deployment နှင့် Container Image ရွေးချယ်မှု:

Nginx နှင့် PHP-FPM တို့ကို ပေါင်းစည်းထားသော webdevops/php-nginx:alpine-3-php7 image ကို အသုံးပြုပါသည်။

MySQL database အတွက် mysql:5.6 image ကို အသုံးပြုပါသည်။

Service Discovery အတွက် Service များ ဖန်တီးခြင်း:

lemp-service: web application ကို NodePort type ဖြင့် expose လုပ်ရန် ဖန်တီးပါသည်။

mysql-service: lemp-wp Deployment အတွင်းရှိ container များက MySQL ကို ၎င်း၏ Service name (ဥပမာ- mysql-service) ဖြင့် အလွယ်တကူ ချိတ်ဆက်နိုင်ရန် ဖန်တီးပါသည်။

အကောင်အထည်ဖော်မှု (Implementation)
ဤ strategy ကို အခြေခံ၍ လိုအပ်သော Kubernetes manifests များကို အောက်ပါအတိုင်း ဖန်တီးရေးသားထားပါသည်။

1. Secrets and ConfigMaps
Bash

# Secrets
kubectl create secret generic mysql-root-pass --from-literal=password=R00t
kubectl create secret generic mysql-user-pass --from-literal=username=kodekloud_gem --from-literal=password=8FmzjvFU6S
kubectl create secret generic mysql-db-url --from-literal=database=kodekloud_db2
kubectl create secret generic mysql-host --from-literal=host=mysql-service
# ConfigMaps
kubectl create configmap php-config --from-literal=php.ini='variables_order = "EGPCS"'
kubectl create configmap index-file-configmap --from-literal=index.php='<?php $servername = getenv("MYSQL_HOST"); $username = getenv("MYSQL_USER"); $password = getenv("MYSQL_PASSWORD"); $dbname = getenv("MYSQL_DATABASE"); $connect = mysqli_connect($servername, $username, $password, $dbname); if (mysqli_connect_error()) { die("Connection failed: " . mysqli_connect_error()); } echo "Connected successfully"; ?>'
2. Deployment YAML (lemp-deployment.yaml)
YAML

apiVersion: apps/v1
kind: Deployment
metadata:
  name: lemp-wp
spec:
  replicas: 1
  selector:
    matchLabels:
      app: lemp-wp-app
  template:
    metadata:
      labels:
        app: lemp-wp-app
    spec:
      containers:
      - name: nginx-php-container
        image: webdevops/php-nginx:alpine-3-php7
        ports:
        - containerPort: 80
        volumeMounts:
        - name: php-config-volume
          mountPath: /opt/docker/etc/php/php.ini
          subPath: php.ini
        - name: index-file-volume
          mountPath: /app/index.php
          subPath: index.php
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      - name: mysql-container
        image: mysql:5.6
        ports:
        - containerPort: 3306
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      volumes:
      - name: php-config-volume
        configMap:
          name: php-config
      - name: index-file-volume
        configMap:
          name: index-file-configmap
3. **Service YAMLs **

---
apiVersion: v1
kind: Service
metadata:
  name: lemp-service
spec:
  type: NodePort
  ports:
    - port: 80
      targetPort: 80
      nodePort: 30008
  selector:
    app: lemp-wp-app
---
apiVersion: v1
kind: Service
metadata:
  name: mysql-service
spec:
  ports:
  - port: 3306
    targetPort: 3306
  selector:
    app: lemp-wp-app

ခေါင်းစဉ်။ Kubernetes ပေါ်တွင် LEMP Stack အသုံးပြု၍ Microservices-based Web Application တစ်ခု တည်ဆောက်ခြင်းဆိုင်ရာ လေ့လာဆန်းစစ်ချက်
နိဒါန်း
ခေတ်မီ web application များ တည်ဆောက်ရာတွင် Monolithic ဗိသုကာပုံစံမှ Microservices ဗိသုကာပုံစံသို့ ပြောင်းလဲလာမှုကြောင့် စီမံခန့်ခွဲမှု လွယ်ကူခြင်း၊ scalability ကောင်းမွန်ခြင်းနှင့် resilience မြင့်မားခြင်း စသည့် အကျိုးကျေးဇူးများ ရရှိစေပါသည်။ ဤစာတမ်းတွင် Kubernetes orchestration engine ကို အသုံးပြု၍ LEMP (Linux, Nginx, MySQL, PHP-FPM) stack ဖြင့် ဝဘ်ဆိုဒ်တစ်ခုကို container deployment ပြုလုပ်ပုံအား အသေးစိတ် လေ့လာဆန်းစစ်တင်ပြထားပါသည်။

Deployment အတွက် လေ့လာတွေ့ရှိချက်များ
LEMP stack အား Kubernetes ပေါ်တွင် deploy လုပ်ရာ၌ Microservices စနစ်၏ အခြေခံမူများဖြစ်သော separation of concerns နှင့် service discovery ကို အလေးထား အကောင်အထည်ဖော်ရန်လိုအပ်ပါသည်။ ဤ deployment strategy တွင် အဓိက အစိတ်အပိုင်း (၂) ခု ပါဝင်ပါသည်။

၁။ Web Tier (Nginx & PHP-FPM) : HTTP requests များကို လက်ခံပြီး PHP script များအား လုပ်ဆောင်ပေးသည့် အပိုင်းဖြစ်သည်။
၂။ Database Tier (MySQL) : application အတွက် data များ သိမ်းဆည်းပေးသည့် အပိုင်းဖြစ်သည်။

ဤ Tier နှစ်ခုကို သီးခြားစီ ခွဲခြား၍ deploy လုပ်ခြင်းဖြင့် scalability နှင့် resilience ပိုမိုကောင်းမွန်စေပါသည်။

အကောင်အထည်ဖော်မည့် အဆင့်များ
LEMP stack ကို အောင်မြင်စွာ deploy လုပ်နိုင်ရန် အောက်ပါအဆင့်များကို စနစ်တကျ လုပ်ဆောင်ရန်လိုအပ်ပါသည်။

Secret နှင့် ConfigMap များ ဖန်တီးခြင်း: MySQL database credentials များကို Secrets များအဖြစ် hard-code မလုပ်ဘဲ သိမ်းဆည်းထားပြီး၊ php.ini နှင့် index.php ကဲ့သို့သော configuration ဖိုင်များကို ConfigMaps များဖြင့် စီမံခန့်ခွဲပါသည်။ ၎င်းသည် configuration ပြောင်းလဲမှုကို လွယ်ကူစေပြီး sensitive data ကိုလည်း လုံခြုံစေပါသည်။

Deployment နှင့် Container Image ရွေးချယ်မှု:

Nginx နှင့် PHP-FPM တို့ကို ပေါင်းစည်းထားသော webdevops/php-nginx:alpine-3-php7 image ကို အသုံးပြုပါသည်။

MySQL database အတွက် mysql:5.6 image ကို အသုံးပြုပါသည်။

Service Discovery အတွက် Service များ ဖန်တီးခြင်း:

lemp-service: web application ကို NodePort type ဖြင့် expose လုပ်ရန် ဖန်တီးပါသည်။

mysql-service: lemp-wp Deployment အတွင်းရှိ container များက MySQL ကို ၎င်း၏ Service name (ဥပမာ- mysql-service) ဖြင့် အလွယ်တကူ ချိတ်ဆက်နိုင်ရန် ဖန်တီးပါသည်။

အကောင်အထည်ဖော်မှု (Implementation)
ဤ strategy ကို အခြေခံ၍ လိုအပ်သော Kubernetes manifests များကို အောက်ပါအတိုင်း ဖန်တီးရေးသားထားပါသည်။

1. Secrets and ConfigMaps
Bash

# Secrets
kubectl create secret generic mysql-root-pass --from-literal=password=R00t
kubectl create secret generic mysql-user-pass --from-literal=username=kodekloud_gem --from-literal=password=8FmzjvFU6S
kubectl create secret generic mysql-db-url --from-literal=database=kodekloud_db2
kubectl create secret generic mysql-host --from-literal=host=mysql-service
# ConfigMaps
kubectl create configmap php-config --from-literal=php.ini='variables_order = "EGPCS"'
kubectl create configmap index-file-configmap --from-literal=index.php='<?php $servername = getenv("MYSQL_HOST"); $username = getenv("MYSQL_USER"); $password = getenv("MYSQL_PASSWORD"); $dbname = getenv("MYSQL_DATABASE"); $connect = mysqli_connect($servername, $username, $password, $dbname); if (mysqli_connect_error()) { die("Connection failed: " . mysqli_connect_error()); } echo "Connected successfully"; ?>'
2. Deployment YAML (lemp-deployment.yaml)
YAML

apiVersion: apps/v1
kind: Deployment
metadata:
  name: lemp-wp
spec:
  replicas: 1
  selector:
    matchLabels:
      app: lemp-wp-app
  template:
    metadata:
      labels:
        app: lemp-wp-app
    spec:
      containers:
      - name: nginx-php-container
        image: webdevops/php-nginx:alpine-3-php7
        ports:
        - containerPort: 80
        volumeMounts:
        - name: php-config-volume
          mountPath: /opt/docker/etc/php/php.ini
          subPath: php.ini
        - name: index-file-volume
          mountPath: /app/index.php
          subPath: index.php
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      - name: mysql-container
        image: mysql:5.6
        ports:
        - containerPort: 3306
        envFrom:
        - secretRef:
            name: mysql-root-pass
        - secretRef:
            name: mysql-user-pass
        - secretRef:
            name: mysql-db-url
        - secretRef:
            name: mysql-host
      volumes:
      - name: php-config-volume
        configMap:
          name: php-config
      - name: index-file-volume
        configMap:
          name: index-file-configmap
3. Service YAMLs
YAML

---
apiVersion: v1
kind: Service
metadata:
  name: lemp-service
spec:
  type: NodePort
  ports:
    - port: 80
      targetPort: 80
      nodePort: 30008
  selector:
    app: lemp-wp-app
---
apiVersion: v1
kind: Service
metadata:
  name: mysql-service
spec:
  ports:
  - port: 3306
    targetPort: 3306
  selector:
    app: lemp-wp-app

နိဂုံး
Kubernetes ပေါ်တွင် LEMP stack ကို အထက်ပါ strategy အတိုင်း deploy လုပ်ခြင်းသည် application ကို fault-tolerant, scalable နှင့် maintainable ဖြစ်စေပါသည်။ Secrets နှင့် ConfigMaps ကို အသုံးပြုခြင်းဖြင့် configuration ကို လုံခြုံစေပြီး Services များဖြင့် component များအကြား decoupling လုပ်ခြင်းသည် Microservices ဗိသုကာ၏ အခြေခံအကျဆုံး အကျိုးကျေးဇူးများကို လက်တွေ့ကျကျ ဖော်ပြနိုင်ပါသည်။ ဤချဉ်းကပ်ပုံသည် DevOps နယ်ပယ်ရှိ application deployment အတွက် ခိုင်မာသော အခြေခံအုတ်မြစ်တစ်ခုကို တည်ဆောက်ပေးပါသည်။


