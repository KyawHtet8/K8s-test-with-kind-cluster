# LAMP Stack သင်ခန်းစာ - Myanmar Students အတွက်

## အခန်း ၁: LAMP Stack ဆိုတာ ဘာလဲ?

### LAMP ရဲ့ အဓိပ္ပါယ်
```
L = Linux    (Operating System - ကွန်ပျူတာ စနစ်)
A = Apache   (Web Server - ဝဘ်ဆာဗာ)
M = MySQL    (Database - ဒေတာဘေ့စ်)
P = PHP      (Programming Language - ပရိုဂရမ်းမင်း ဘာသာစကား)
```

### Real Life နှိုင်းယှဉ်ချက်
```
LAMP Stack = တစ်ခု ပြီးပြည့်စုံတဲ့ စာသင်ကျောင်း

Linux   = ကျောင်းရဲ့ အခြေခံ အဆောက်အအုံ (foundation)
Apache  = ကျောင်းရဲ့ ရှေ့တံခါး (front door) - လူတွေ ဝင်ထွက်တဲ့ နေရာ
MySQL   = ကျောင်းရဲ့ စာကြည့်တိုက် (library) - ဒေတာတွေ သိမ်းတဲ့ နေရာ
PHP     = ဆရာ (teacher) - အလုပ်တွေ လုပ်ပေးတဲ့ သူ
```

## အခန်း ၂: Traditional LAMP vs Kubernetes LAMP

### Traditional LAMP (စာရေးစက် တစ်လုံးမှာ အကုန်လုံး)
```
┌─────────────────────────────────┐
│         One Server              │
│  ┌─────┐ ┌───────┐ ┌─────────┐  │
│  │Linux│ │Apache │ │  MySQL  │  │
│  │ OS  │ │  +    │ │Database │  │
│  │     │ │  PHP  │ │         │  │
│  └─────┘ └───────┘ └─────────┘  │
└─────────────────────────────────┘
```

### Kubernetes LAMP (Container တွေခွဲပြီး)
```
┌──────────────────────────────────────────────┐
│                Kubernetes Pod                 │
│  ┌─────────────────┐  ┌─────────────────────┐│
│  │ Container 1     │  │   Container 2       ││
│  │ ┌─────────────┐ │  │ ┌─────────────────┐ ││
│  │ │   Apache    │ │  │ │     MySQL       │ ││
│  │ │     +       │ │  │ │   Database      │ ││
│  │ │    PHP      │ │  │ │                 │ ││
│  │ └─────────────┘ │  │ └─────────────────┘ ││
│  └─────────────────┘  └─────────────────────┘│
└──────────────────────────────────────────────┘
```

## အခန်း ၃: Data Flow (အချက်အလက် စီးဆင်းမှု)

### Web Request လုပ်ငန်းစဉ် (အဆင့် ၈ ဆင့်)

```
[1] User Browser
     ↓ "http://website.com/index.php"
[2] Internet/Network
     ↓ 
[3] Kubernetes NodePort Service (Port 30008)
     ↓ "ဒီ request ကို ဘယ် Pod ကို ပို့ရမလဲ?"
[4] Apache Web Server (Container 1, Port 80)
     ↓ "index.php ဖိုင် လိုတယ်"  
[5] PHP Engine (Container 1 အတွင်း)
     ↓ "MySQL ကနေ ဒေတာ လိုတယ်"
[6] MySQL Database (Container 2, Port 3306)
     ↓ "SELECT * FROM users" 
[7] Response စီးပြန်သွား
     ↓ Database → PHP → Apache → Service → Internet
[8] User Browser မှာ ရလဒ် မြင်ရတယ်
```

## အခန်း ၄: Kubernetes Components အသေးစိတ်

### 1. ConfigMap (ပြင်ဆင်မှု ဖိုင်တွေ)
```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: php-config
data:
  php.ini: |
    variables_order = "EGPCS"
```

**Real Life Example**: 
- ConfigMap = ကျောင်းရဲ့ စည်းမျဉ်း စည်းကမ်း စာအုပ်
- PHP container က ဒီ စည်းမျဉ်းတွေကို ဖတ်ပြီး လုပ်တယ်

### 2. Secret (လုံခြုံရေး အချက်အလက်)
```yaml
apiVersion: v1
kind: Secret
data:
  mysql-password: d3BfcGFzc3dvcmQ=  # base64 encoded
```

**Real Life Example**:
- Secret = ကိုယ်ရေး ကိုယ်တာ အချက်အလက်တွေ သိမ်းထားတဲ့ ရေခဲသေတ္တာ
- Base64 encoding = အခြေခံ လုံခြုံရေး (တံခါး သော့ခတ်ထားတာ လို)

### 3. Deployment (Application ကို စီမံခန့်ခွဲမှု)
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: lamp-wp
spec:
  replicas: 1
```

**Real Life Example**:
- Deployment = ကျောင်းရဲ့ စီမံခန့်ခွဲရေး ကမ္မတီ
- Pod တွေ ဘယ်လို run မယ်၊ ဘယ်နှစ်ခု လိုမယ် ဆိုတာ ဆုံးဖြတ်တယ်

### 4. Service (Network Traffic စီမံခန့်ခွဲမှု)
```yaml
apiVersion: v1
kind: Service
metadata:
  name: lamp-service
spec:
  type: NodePort
  ports:
    - nodePort: 30008
```

**Real Life Example**:
- Service = ကျောင်းရဲ့ လုံခြုံရေး အစောင့် (security guard)
- ပြင်ပက လာတဲ့ လူတွေကို မှန်ကန်တဲ့ နေရာကို ပို့ပေးတယ်

## အခန်း ၅: Container Communication (Container တွေ ဆက်သွယ်ပုံ)

### Same Pod Network Sharing
```
Pod = တစ်လုံးတည်း အိမ်
Container 1 = အခန်း ၁ (Apache+PHP)
Container 2 = အခန်း ၂ (MySQL)

အိမ်မှာ:
✓ IP Address တူတယ် (194.168.1.10)
✓ Network interface တူတယ် (wifi router တူတယ်)  
✓ Port space share လုပ်တယ် (Container 1 က port 80, Container 2 က port 3306)
✗ File system မတူဘူး (အခန်းခွဲ မှာ file တွေ မတူဘူး)
```

### Connection Methods

#### Method 1: Unix Socket (File-based)
```
Apache Container → /run/mysqld/mysqld.sock → MySQL Container
                   ↑
                 ဒီ file မရှိဘူး! (Container တွေ file system မတူလို့)
```

#### Method 2: TCP Connection (Network-based)
```
Apache Container → localhost:3306 (TCP) → MySQL Container
                   ↑
                 ဒါ အလုပ်ဖြစ်တယ်! (Network share လုပ်လို့)
```

## အခန်း ၆: Environment Variables (ပတ်ဝန်းကျင် ကိန်းရှင်များ)

### Environment Variable Chain
```
[Kubernetes Secret] → [Container Environment] → [PHP Code]
       ↓                      ↓                    ↓
   mysql-password         MYSQL_PASSWORD      getenv('MYSQL_PASSWORD')
   (base64 encoded)       (decoded value)     (PHP function call)
```

### PHP Code Example
```php
// မှားတဲ့ နည်းလမ်း
$password = "hardcoded_password";  // ❌ လုံခြုံမှု မရှိ

// မှန်တဲ့ နည်းလမ်း  
$password = getenv('MYSQL_PASSWORD');  // ✅ လုံခြုံတယ်
```

## အခန်း ၇: Common Problems & Solutions

### Problem 1: "Index of /" မြင်ရခြင်း
```
အကြောင်းရင်း: index.php ဖိုင် မရှိတာ (သို့) PHP မ process ဖြစ်တာ
ဖြေရှင်းနည်း: 
1. ဖိုင် ရှိမရှိ စစ်ပါ: kubectl exec ... ls /app/
2. PHP working မရှိ စစ်ပါ: echo "<?php phpinfo(); ?>" > test.php
```

### Problem 2: "Unable to Connect" Error  
```
အကြောင်းရင်း: Environment variables မရှိတာ (သို့) MySQL connection method မှား
ဖြေရှင်းနည်း:
1. ENV variables စစ်ပါ: kubectl exec ... env | grep MYSQL
2. TCP connection သုံးပါ: mysqli_connect($host, $user, $pass, $db, 3306)
```

### Problem 3: "Connection Refused" on localhost:30008
```
အကြောင်းရင်း: NodePort က Node IP မှာပဲ available
ဖြေရှင်းနည်း:
1. Node IP သုံးပါ: kubectl get nodes -o wide
2. Port forward သုံးပါ: kubectl port-forward svc/lamp-service 8080:80
```

## အခန်း ၈: Best Practices (အကောင်းဆုံး အလေ့အကျင့်များ)

### Development Environment
```
✓ Multi-container pod သုံးလို့ ရတယ် (testing လွယ်လို့)
✓ emptyDir volume သုံးလို့ ရတယ် (temporary data လို့)
✓ Simple secrets သုံးလို့ ရတယ်
```

### Production Environment  
```
✓ Separate deployments (Apache ခွဲ၊ MySQL ခွဲ)
✓ PersistentVolume သုံးရမယ် (data မဆုံးအောင်)  
✓ Resource limits set လုပ်ရမယ်
✓ Health checks ထည့်ရမယ်
✓ Network policies သုံးရမယ်
✓ Secret rotation လုပ်ရမယ်
```

## အခန်း ၉: Real World Applications

### E-commerce Website Example
```
LAMP Stack Components:
- Linux: Ubuntu/CentOS server
- Apache: Handle HTTP requests (product pages, checkout)
- MySQL: Store products, users, orders, inventory
- PHP: Business logic (payment processing, user authentication)
```

### Content Management System (CMS)
```  
LAMP Stack Components:
- Linux: Operating system foundation
- Apache: Serve web pages, handle uploads  
- MySQL: Store articles, users, comments, media metadata
- PHP: Content rendering, admin panel, user interactions
```

## အခန်း ၁០: Career Relevance

### Job Roles ဆိုင်ရာ အသုံးဝင်မှု

#### Web Developer
```
- PHP programming skills
- MySQL database design  
- Apache configuration
- Linux command line
```

#### DevOps Engineer
```
- Containerization (Docker/Kubernetes)
- Infrastructure automation
- Monitoring and logging
- CI/CD pipelines
```

#### System Administrator
```
- Server management
- Security hardening
- Performance optimization  
- Backup and recovery
```

## နိဂုံး: Key Takeaways

### Technical Skills
1. **Container orchestration** နားလည်ခြင်း
2. **Multi-service communication** နားလည်ခြင်း  
3. **Configuration management** လုပ်နိုင်ခြင်း
4. **Troubleshooting methodology** သိခြင်း

### Problem-Solving Approach
1. **Systematic debugging** - အဆင့်ဆင့် စစ်ဆေးခြင်း
2. **Root cause analysis** - အရင်း အမြစ် ရှာဖွေခြင်း
3. **Documentation reading** - စာရွက်စာတမ်းတွေ ဖတ်ခြင်း
4. **Hands-on practice** - လက်တွေ့ လုပ်ဆောင်ခြင်း

LAMP Stack က web development ရဲ့ အခြေခံ အုတ်မြစ် ဖြစ်တယ်။ ဒါကို မှန်ကန်စွာ နားလည်ရင် modern web technologies တွေ သင်ယူဖို့ လွယ်ကူသွားမယ်။