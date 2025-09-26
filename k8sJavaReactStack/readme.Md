*****ခွဲထုတ်ခြင်း (Decoupling): Backend နှင့် Frontend ကို သီးခြား Deployment ဖြင့် ခွဲထုတ်ထားသောကြောင့် Application နှစ်ခုစလုံးကို တစ်ခုနှင့်တစ်ခု မထိခိုက်စေဘဲ သီးခြားစီ Update လုပ်နိုင်ပါတယ်။

*****ဆက်သွယ်မှု (Communication): Frontend သည် backend-api-service:8080 (Service Name) ကို သုံးပြီး Backend ကို API Call များ ပြုလုပ်ပါမယ်။ Kubernetes က IP Address အပြောင်းအလဲရှိရင်တောင် Service Name ကို သုံးတာကြောင့် လွယ်ကူစွာ ချိတ်ဆက်ပေးထားပါတယ်။

ဝန်ထုတ်ဝန်ပိုး မျှဝေခြင်း (Load Balancing): Service များသည် ၎င်းတို့ နောက်ကွယ်မှ Pod များအားလုံးဆီသို့ request များကို အညီအမျှ မျှဝေပေးပို့ခြင်း (Load Balancing) ကို အလိုအလျောက် လုပ်ဆောင်ပေးပါတယ်။


*****Container Image ၏ မူ (The Immutability Concept)******
Single Responsibility: Java Container တစ်ခုတည်းမှာ Java Application ကိုသာ ထည့်ရပြီး၊ React Container မှာ Nginx နှင့် Build Files ကိုသာ ထည့်ရပါမယ်။ Container တစ်ခုသည် Process တစ်ခုတည်းကိုသာ ကိုင်တွယ်သင့်ပါတယ်။

Immutable Infrastructure: Pod တွေ ပြန်စတင်တဲ့အခါ အမြဲတမ်း Build Image ကနေ ပြန်စတင်တဲ့အတွက် Container အတွင်းမှာ data modify လုပ်တာမျိုးကို ရှောင်ရှားရပါမယ်။ Data Persistent ဖြစ်ဖို့ Volume တွေကိုသာ အသုံးပြုရပါမယ်။

*****Application Configuration ၏ စီမံခန့်ခွဲမှု (Configuration Management)*****
ConfigMap vs. Secret: API URL များ၊ feature flags များကဲ့သို့ မလျှို့ဝှက်သော configuration များကို ConfigMap ဖြင့် စီမံခန့်ခွဲရပါတယ်။ Database Password ကဲ့သို့သော လျှို့ဝှက်ဒေတာ များကိုတော့ Secret ဖြင့် စီမံခန့်ခွဲရပါမယ်။

Environment Variables: ဤ ConfigMap နှင့် Secret data များကို Container အတွင်းသို့ Volume mount အနေဖြင့်လည်းကောင်း၊ Environment Variable (Env Var) အနေဖြင့်လည်းကောင်း inject လုပ်ပေးခြင်းဖြင့် Application Code ကို ပြင်ဆင်စရာမလိုဘဲ Configuration ကို ပြောင်းလဲနိုင်ပါတယ်။

