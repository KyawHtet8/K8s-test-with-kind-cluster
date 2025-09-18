Kubernetes (K8s) အတွက် Namespace နဲ့ Service Expose အကြောင်း README ဖိုင်ကို မြန်မာဘာသာနဲ့ အနှစ်ချုပ် ရေးပေးထားပါတယ်။

## 📖 Namespace နဲ့ Service Expose အကြောင်း

### ✨ ဘာကြောင့် လိုအပ်တာလဲ?

Kubernetes (K8s) မှာ **Container တွေရဲ့ IP Address က မတည်ငြိမ်ပါဘူး။** Container တစ်ခု ပျက်သွားပြီး ပြန်တက်လာရင် IP Address အသစ်တစ်ခု ရရှိပါတယ်။ ဒီလို မတည်ငြိမ်တဲ့ သဘောသဘာဝကြောင့် Service တွေ အချင်းချင်း ဆက်သွယ်တဲ့အခါ IP Address ကို တိုက်ရိုက် သုံးလို့ မရပါဘူး။ ဒီပြဿနာကို ဖြေရှင်းဖို့အတွက် **Namespace** နဲ့ **Service** ကို အသုံးပြုပါတယ်။

---

### 📦 Namespace

Namespace ဆိုတာ Kubernetes cluster အတွင်းမှာရှိတဲ့ Resource တွေကို ခွဲခြားစီမံဖို့အတွက် **Virtual Cluster** လိုမျိုး အသုံးပြုတဲ့ နည်းလမ်းတစ်ခု ဖြစ်ပါတယ်။

- **အဓိက အကျိုးကျေးဇူးများ:**
  - **ခွဲခြားမှု:** 💡 Project တစ်ခုချင်းစီ၊ Environment (ဥပမာ- Production, Development) တစ်ခုချင်းစီအတွက် သီးခြားစီ ခွဲထားနိုင်ပါတယ်။
  - **စီမံခန့်ခွဲမှု လွယ်ကူခြင်း:** 🧑‍💻 Resource တွေကို အစုလိုက် အစုလိုက် စီမံခန့်ခွဲရတာ ပိုလွယ်ကူစေပါတယ်။
  - **အမည် ထပ်နေမှုမှ ကာကွယ်ခြင်း:** 🛑 Namespace တစ်ခုစီမှာ Resource တွေရဲ့ နာမည်က ထပ်နေလို့ရပါတယ်။ ဥပမာ- 'default' Namespace နဲ့ 'production' Namespace နှစ်ခုလုံးမှာ 'web-app' ဆိုတဲ့ Deployment ရှိနေနိုင်ပါတယ်။

---

### 🌐 Service

Service ကတော့ မတည်ငြိမ်တဲ့ Container IP Address တွေကို **တည်ငြိမ်တဲ့ Domain Name** (Fully Qualified Domain Name - FQDN) တစ်ခုနဲ့ ချိတ်ဆက်ပေးတဲ့ နည်းလမ်းတစ်ခုပါ။

- **အဓိက အကျိုးကျေးဇူးများ:**
  - **တည်ငြိမ်တဲ့ Domain Name:** ✨ Service တစ်ခု ဖန်တီးလိုက်ရင် K8s ကနေ ဒီ Service အတွက် **`[service-name].[namespace].svc.cluster.local`** ဆိုတဲ့ တည်ငြိမ်တဲ့ Domain Name တစ်ခု ပေးပါတယ်။
  - **Load Balancing (ဝန်မျှခြင်း):** ⚖️ Service က ၎င်းနဲ့ ချိတ်ဆက်ထားတဲ့ Container တွေ (Pods) ကို ဝင်လာတဲ့ Request တွေကို အလိုအလျောက် မျှဝေပေးပါတယ်။ ဒါကြောင့် Pod တစ်ခု ပျက်သွားရင်တောင် တခြား Pod တွေက ဆက်လက် အလုပ်လုပ်နေမှာ ဖြစ်ပါတယ်။
  - **Discovery (ရှာဖွေမှု):** 🔍 Service တွေ အချင်းချင်း ဆက်သွယ်တဲ့အခါ IP Address အစား FQDN ကို အသုံးပြုနိုင်တာကြောင့် Container တွေရဲ့ တည်နေရာကို စိတ်ပူစရာ မလိုတော့ပါဘူး။

---

### 📝 အနှစ်ချုပ် Key Note

- **Container IP:** မတည်ငြိမ်၊ အမြဲ ပြောင်းလဲနိုင်တယ်။
- **Namespace:** Resource တွေ ခွဲခြားစီမံဖို့ Virtual Cluster တစ်ခုလို အသုံးပြုတယ်။
- **Service:** မတည်ငြိမ်တဲ့ Container IP ကို တည်ငြိမ်တဲ့ Domain Name (FQDN) နဲ့ ချိတ်ဆက်ပေးပြီး **ဝန်မျှခြင်း (Load Balancing)** လုပ်ဆောင်ပေးတယ်။
- **Domain Naming:** K8s Cluster အတွင်းမှာ Service တစ်ခုရဲ့ Domain Name က **`[service-name].[namespace].svc.cluster.local`** ပုံစံ ဖြစ်ပါတယ်။
- **ပြဿနာ ဖြေရှင်းနည်း:** 🔑 K8s မှာ Container တွေရဲ့ မတည်ငြိမ်တဲ့ IP ပြဿနာကို **Service Expose** လုပ်ပြီး **Fully Qualified Domain Name (FQDN)** ကို အသုံးပြုခြင်းဖြင့် အပြည့်အဝ ဖြေရှင်းနိုင်ပါတယ်။

Pods → short-lived, dynamic IP

Services → stable endpoint + load balancing

Namespace → isolate resources, avoid naming conflicts

FQDN → <service>.<namespace>.svc.cluster.local (DNS inside cluster)

Best Practice: Always use Service name or FQDN instead of direct Pod IP

kubectl expose deployment grafana-deployment \
 --type=NodePort \
 --port=3000 \
 --target-port=3000 \
 --name=grafana-service

curl <service-name>.<namespace>.svc.cluster.local

Example:

Service: grafana-service

Namespace: monitoring

FQDN: grafana-service.monitoring.svc.cluster.local

Application inside cluster can call:

## http://grafana-service.monitoring.svc.cluster.local:3000

→ Even if pod IPs change, service DNS stays the same.
