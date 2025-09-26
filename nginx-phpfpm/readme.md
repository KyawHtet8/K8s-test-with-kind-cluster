Lab Test တွင် Multi-Container Communication (Nginx-PHP-FPM) အဆင့်ဆင့်
ဒီ Test Case ဟာ Kubernetes Pod ရဲ့ Shared Network Namespace သဘောတရားကို အဓိက အသုံးပြုထားတာ ဖြစ်ပါတယ်။

၁။ Network Hit ၏ စတင်ခြင်း (NodePort 30012)
အသုံးပြုသူတစ်ဦးက Browser မှာ NodeIP:30012 ကို request လုပ်လိုက်ချိန်မှာ စတင်ပါတယ်။

Service Action: mysql-phpfpm-service (NodePort: 30012) က ဝင်လာတဲ့ request ကို ကြားဖြတ်ဖမ်းယူပြီး Pod ရဲ့ Label Selector (app: nginx-phpfpm) နဲ့ ကိုက်ညီတဲ့ Pod ဆီကို လမ်းကြောင်းပြောင်းပေးပါတယ်။

Target Port: request ကို Pod ထဲက nginx-container ရဲ့ listen port ဖြစ်တဲ့ 8098 ဆီကို ရောက်ရှိစေပါတယ်။

၂။ Nginx ၏ Configuration စစ်ဆေးခြင်း
Request ဟာ Nginx Container ထဲက Port 8098 ကို ရောက်လာချိန်မှာ Nginx က ConfigMap မှ ရရှိထားသော /etc/nginx/conf.d/default.conf ကို စစ်ဆေးပါတယ်။

Document Root: Nginx က request သည် /var/www/html Document Root ကို တောင်းဆိုနေကြောင်း သိရှိပြီး index index.php ကို ရှာဖွေပါတယ်။

Dynamic Check: index.php file ကို တွေ့တာနဲ့ Nginx ဟာ ဒါဟာ static file မဟုတ်ဘဲ dynamic PHP code ဖြစ်ကြောင်း ဆုံးဖြတ်လိုက်ပါတယ်။ location ~ \.php$ block က စတင် အသက်ဝင်ပါတော့တယ်။

၃။ Shared Network ဖြင့် လွှဲပြောင်းပေးခြင်း (FastCGI Pass)
ဒီအဆင့်က အဓိက အရေးအကြီးဆုံးပဲ။ Nginx က PHP code ကို Execute မလုပ်နိုင်တဲ့အတွက် PHP-FPM ကို လွှဲပြောင်းပေးရပါမယ်။

fastcgi_pass 127.0.0.1:9000;: 127.0.0.1 (သို့မဟုတ် localhost) ဆိုတာဟာ Nginx Container နဲ့ PHP-FPM Container တို့ shared လုပ်ထားတဲ့ Network Interface ရဲ့ IP ဖြစ်ပါတယ်။ ဒါကြောင့် Nginx က "ငါ့အိမ်ထဲက Port 9000 ကို listen လုပ်နေတဲ့ Process ဆီကို ပို့ပေးပါ" လို့ ညွှန်ကြားလိုက်တာပဲ ဖြစ်ပါတယ်။

PHP-FPM Action: php-fpm-container ထဲမှာ Port 9000 မှာ listen လုပ်နေတဲ့ PHP-FPM Process က Nginx ဆီက ရောက်လာတဲ့ request data ကို လက်ခံရယူလိုက်ပါတယ်။

၄။ Shared Volume မှ Code Execution
PHP-FPM Process က request ကို လက်ခံရရှိပြီးနောက်၊ code ကို execute လုပ်ပါတယ်။

Code Reading: PHP-FPM က shared-files emptyDir Volume ကို mount လုပ်ထားတဲ့ /var/www/html လမ်းကြောင်းကနေ index.php file ကို ဖတ်ပြီး code များကို execute လုပ်ဆောင်ပါတယ်။

Result Return: Code ကို execute လုပ်ရာကနေ ထွက်လာတဲ့ HTML Result ကို PHP-FPM က FastCGI Protocol ကနေတဆင့် Nginx ဆီကို Port 9000 ကနေတဆင့် ပြန်လည် ပေးပို့ပေးပါတယ်။

၅။ Response အဆုံးသတ်ခြင်း
Nginx သည် PHP-FPM ဆီက ရရှိလာတဲ့ Final HTML Output ကို လက်ခံရယူပြီး၊ ၎င်းကို NodePort 30012 မှတစ်ဆင့် မူလ Client ဆီသို့ ပြန်လည်ပေးပို့ခြင်းဖြင့် Request Cycle တစ်ခု ပြီးဆုံးသွားပါတယ်။

ဒါဟာ Kubernetes ရဲ့ Pod Networking၊ Configuration Management (ConfigMap) နဲ့ Data Sharing (Volume) တို့ ပေါင်းစပ်ပြီး Dynamic Web Application တစ်ခုကို အလုပ်လုပ်စေတဲ့ Mechanism ဖြစ်ပါတယ်။


***** flow *****
Communication Flow
      1. User Request
        Via: NodeIP:30012
        Targets: nginx-phpfpm-service
      2. Service Forwards
        To: nginx-phpfpm Pod (on Port 8098 of nginx-container)
      3. Nginx Processes Request
        Looks for: index.php in /var/www/html
        Uses: nginx-config (from ConfigMap)
        Detects: .php file -> triggers fastcgi_pass
      4. Nginx to PHP-FPM
        Method: fastcgi_pass
        Address: 127.0.0.1:9000 (via shared Pod network)
        Targets: php-fpm-container (listening on Port 9000)
      5. PHP-FPM Executes PHP
        Reads: index.php from /var/www/html (shared-files volume)
        Processes: PHP code
        Generates: HTML output
      6. PHP-FPM Returns Response
        To: Nginx (via 127.0.0.1:9000)
      7. Nginx Serves Final Response
        To: User (via NodeIP:30012)