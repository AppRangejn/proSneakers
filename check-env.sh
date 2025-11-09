#!/bin/bash
echo "🔍 Перевіряю середовище розробки ProKrosivski..."
echo "-----------------------------------------------"


echo "🧩 Контейнери:"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep prokrosivski
echo


echo "⚙️ Перевіряю Laravel..."
docker exec -it prokrosivski-laravel php artisan migrate:status > /dev/null 2>&1 && echo "✅ Міграції OK" || echo "❌ Міграції недоступні"
docker exec -it prokrosivski-laravel php artisan route:list > /dev/null 2>&1 && echo "✅ Маршрути OK" || echo "❌ Проблема з маршрутами"

docker exec -it prokrosivski-laravel php -r "try{new PDO('mysql:host=mysql;dbname=prokrosivski','root','root');echo '✅ MySQL підключено\n';}catch(Exception \$e){echo '❌ MySQL не підключено\n';}"

docker exec -it prokrosivski-laravel php -r "try{\$r=new Redis();\$r->connect('redis',6379);echo '✅ Redis підключено\n';}catch(Exception \$e){echo '❌ Redis не підключено\n';}"


echo
echo "🌐 Перевіряю API..."
API_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/api/test)
if [ "$API_RESPONSE" == "200" ]; then
  echo "✅ API відповідає (http://localhost:8080/api/test)"
else
  echo "❌ API не відповідає (код $API_RESPONSE)"
fi


echo
echo "📬 Перевірка Mailpit..."
MAILPIT_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8025)
if [ "$MAILPIT_RESPONSE" == "200" ]; then
  echo "✅ Mailpit працює (http://localhost:8025)"
else
  echo "❌ Mailpit недоступний"
fi


echo
echo "🖥️ Перевірка фронтенду..."
FRONT_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080)
if [ "$FRONT_RESPONSE" == "200" ] || [ "$FRONT_RESPONSE" == "304" ]; then
  echo "✅ Next.js працює (http://localhost:8080)"
else
  echo "❌ Next.js недоступний (код $FRONT_RESPONSE)"
fi

echo
echo "-----------------------------------------------"
echo "🏁 Перевірка завершена."
