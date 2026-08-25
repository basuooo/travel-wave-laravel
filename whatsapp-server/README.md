# TravelWave WhatsApp Gateway Service (Baileys Node.js)

## 🚀 Quick Setup Instructions on Server (Hostinger / VPS)

### 1. Navigate to the service folder:
```bash
cd /home/u351427424/domains/travelwave-ras.com/public_html/whatsapp-server
```

### 2. Install dependencies:
```bash
npm install
```

### 3. Start the gateway service using PM2 (for 24/7 background uptime):
```bash
npm install -g pm2
pm2 start index.js --name "whatsapp-gateway"
pm2 save
pm2 startup
```

### 4. Verification:
The gateway runs on port `3001`. You can test it via browser or cURL:
`http://localhost:3001/health` -> `{"status":"ok"}`
