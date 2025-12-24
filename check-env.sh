#!/bin/bash
echo "🔍 Перевіряю середовище розробки ProKrosivski..."
echo "-----------------------------------------------"

# 1. Стан контейнерів
echo "🧩 Стан Docker:"
docker ps --format "table {{.Names}}\t{{.Status}}" | grep prokrosivski || echo "❌ Контейнери не запущені!"
echo

# 2. Перевірка Laravel (внутрішня)
echo "⚙️  Внутрішня перевірка Laravel (в контейнері):"

# Міграції
if docker exec prokrosivski-laravel php artisan migrate:status > /dev/null 2>&1; then
  echo "✅ Міграції OK"
else
  echo "❌ Міграції не виконані або помилка БД"
fi

# Зв'язок зі сховищем (важливо для фото кросівок)
if docker exec prokrosivski-laravel ls public/storage > /dev/null 2>&1; then
  echo "✅ Storage link OK"
else
  echo "⚠️  Storage link відсутній (виконай php artisan storage:link)"
fi

# БД через PHP
docker exec prokrosivski-laravel php -r "try{new PDO('mysql:host=mysql;dbname=prokrosivski','root','root');echo '✅ MySQL підключено\n';}catch(Exception \$e){echo '❌ MySQL помилка: '.\$e->getMessage().'\n';}"

# Redis
docker exec prokrosivski-laravel php -r "try{\$r=new Redis();\$r->connect('redis',6379);echo '✅ Redis підключено\n';}catch(Exception \$e){echo '❌ Redis не підключено\n';}"

echo
echo "🌐 Зовнішня перевірка (через Nginx):"

# API (перевіряємо хоча б корінь або 404/401, що означає, що сервер відповів)
API_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/api)
if [ "$API_CODE" -ne 000 ]; then
  echo "✅ API (Nginx -> PHP-FPM) доступне (Код: $API_CODE)"
else
  echo "❌ API недоступне (Nginx лежить?)"
fi

# Next.js
FRONT_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080)
if [ "$FRONT_CODE" == "200" ] || [ "$FRONT_CODE" == "304" ]; then
  echo "✅ Next.js працює"
else
  echo "❌ Next.js видає код $FRONT_CODE"
fi

echo "-----------------------------------------------"
echo "🏁 Перевірка завершена."