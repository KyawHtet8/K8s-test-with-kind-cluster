# ==============================================
# Kind Cluster Configuration
# API Version: kind.x-k8s.io/v1alpha4
# ရည်ရွယ်ချက်: ဆော့ဖ်ဝဲဖွံ့ဖြိုးရေးအတွက် local Kubernetes cluster
# ==============================================

apiVersion: kind.x-k8s.io/v1alpha4

# Cluster nodes configuration
# ----------------------------
nodes:
  # Control Plane Node (Master)
  # တာဝန်များ: API Server, etcd, Controller Manager, Scheduler
  - role: control-plane
    
    # Port Mapping Configuration
    # Host machine ကနေ Kubernetes services တွေကို access လုပ်နိုင်အောင်
    # Format: containerPort:hostPort:protocol
    extraPortMappings:
      # Mapping 1: Web Application Access
      # ရည်ရွယ်ချက်: Port 30001 မှာ NodePort services တွေကို ဖွင့်ပေးခြင်း
      # အသုံးပြုပုံ: nodePort: 30001 သုံးထားတဲ့ services တွေကို localhost:30001 ကနေ access လုပ်နိုင်မယ်
      - containerPort: 30001  # Container/node အတွင်းက port
        hostPort: 30001       # သင့်စက်ထဲက port (localhost)
        protocol: TCP         # Network protocol (TCP/UDP)
        # ဥပမာ: http://localhost:30001
        
      # နောက်ထပ် port mappings တွေ ထည့်နိုင်ပါတယ်:
      # - containerPort: 30002
      #   hostPort: 30002
      #   protocol: TCP
      #   comment: အခြား service အတွက်

  # Worker Node 1
  # တာဝန်များ: Application workloads (pods) တွေကို run ပေးခြင်း
  # Worker အများကြီးသုံးရင် load balancing နဲ့ high availability ရမယ်
  - role: worker
    # မှတ်ချက်: Worker nodes တွေက port mappings သိပ်မလိုအပ်ဘူး
    # ဘာလို့လဲဆိုတော့ services တွေက control-plane node ကနေ expose လုပ်လို့

  # Worker Node 2  
  # Resource distribution ပိုကောင်းအောင် နဲ့
  # multi-node scenarios တွေ test လုပ်ဖို့ အပိုထည့်ထားတယ်
  - role: worker

# ==============================================
# အသုံးပြုပုံ ဥပမာများ:
# ==============================================

# 1. Cluster ဖန်တီးဖို့:
#    kind create cluster --name my-cluster --config kind-config.yaml

# 2. Mapped port ကိုသုံးတဲ့ service deploy လုပ်ဖို့:
#    apiVersion: v1
#    kind: Service
#    metadata:
#      name: my-service
#    spec:
#      type: NodePort
#      ports:
#      - port: 80
#        targetPort: 80
#        nodePort: 30001  # ← ဒါက localhost:30001 ကနေ access လုပ်နိုင်မယ်

# 3. Application ကို access လုပ်ဖို့:
#    curl http://localhost:30001
#    (သို့) browser ကနေ http://localhost:30001 ကိုဖွင့်

# ==============================================
# Networking ရှင်းလင်းချက်:
# ==============================================

# Host Machine (သင့်စက်) → localhost:30001
#     ↓
# Docker Container (Control-Plane Node) → port 30001
#     ↓
# Kubernetes Service → NodePort:30001
#     ↓
# Application Pod → ContainerPort:8080 (ဥပမာ)

# ==============================================
# ပြဿနာဖြေရှင်းနည်းများ:
# ==============================================

# - Port 30001 သုံးနေပြီးရင် hostPort ကိုပြောင်းပါ
# - အခြား applications တွေ port 30001 မသုံးထားကြောင်း သေချာပါ
# - စစ်ဆေးရန်: netstat -tln | grep 30001
# - Port mapping အလုပ်မလုပ်ရင် Docker ကို restart လုပ်ပါ

# ==============================================
# အကောင်းဆုံးအလေ့အထများ:
# ==============================================

# - NodePort services တွေအတွက် 30000-32767 range ထဲက ports တွေသုံးပါ
# - Port mapping တစ်ခုချင်းစီရဲ့ ရည်ရွယ်ချက်ကို မှတ်သားထားပါ
# - Service အမျိုးမျိုးအတွက် mapping အမျိုးမျိုးသုံးပါ
# - Production-like setup အတွက် ingress controller သုံးဖို့စဉ်းစားပါ

Key note

# ရည်ရွယ်ချက်: Kubernetes NodePort services တွေကို host machine ကနေ access လုပ်နိုင်အောင်
# Services: nodePort: 30001 သုံးထားတဲ့ applications တွေ
# Access: http://localhost:30001

# နည်းပညာ: Host port ကို container port နဲ့ ချိတ်ဆက်ပေးခြင်း
# Protocol: Web traffic အတွက် TCP, streaming အတွက် UDP
# Range: 30000-32767 (standard NodePort range)

# ဥပမာ: web-app service → nodePort: 30001 → localhost:30001
# ဥပမာ: api-service → nodePort: 30002 → localhost:30002

# မှတ်ချက်: Port conflict ရှိမရှိ စစ်ဆေးရန် - netstat -tln | grep 30001
# မှတ်ချက်: Port သုံးနေပြီးရင် hostPort ကိုပြောင်းပါ
# မှတ်ချက်: Config changes တွေလုပ်ပြီးရင် Docker restart လုပ်ဖို့လိုနိုင်တယ်