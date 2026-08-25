const express = require('express');
const cors = require('cors');
const QRCode = require('qrcode');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    delay
} = require('@whiskeysockets/baileys');

const app = express();
const PORT = process.env.PORT || 3001;

app.use(cors());
app.use(express.json());

// Session Storage in memory & on disk
const sessions = new Map();
const qrCodes = new Map();
const sessionStatus = new Map();

const SESSIONS_DIR = path.join(__dirname, 'sessions');
if (!fs.existsSync(SESSIONS_DIR)) {
    fs.mkdirSync(SESSIONS_DIR, { recursive: true });
}

// Logger
const logger = pino({ level: 'silent' });

/**
 * Initialize or get a Baileys session for an account ID
 */
async function initSession(accountId) {
    const sessionPath = path.join(SESSIONS_DIR, `session_${accountId}`);

    if (sessions.has(accountId)) {
        const existingSocket = sessions.get(accountId);
        if (existingSocket && sessionStatus.get(accountId) === 'connected') {
            return existingSocket;
        }
    }

    sessionStatus.set(accountId, 'connecting');

    const { state, saveCreds } = await useMultiFileAuthState(sessionPath);
    const { version } = await fetchLatestBaileysVersion();

    const sock = makeWASocket({
        version,
        logger,
        printQRInTerminal: false,
        auth: state,
        browser: ['TravelWave ERP', 'Chrome', '1.0.0'],
        generateHighQualityLinkPreview: true,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            try {
                const qrImageBase64 = await QRCode.toDataURL(qr);
                qrCodes.set(accountId, qrImageBase64);
                sessionStatus.set(accountId, 'qr_ready');
            } catch (err) {
                console.error(`QR Generation Error for Account ${accountId}:`, err);
            }
        }

        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            qrCodes.delete(accountId);

            if (shouldReconnect) {
                sessionStatus.set(accountId, 'reconnecting');
                setTimeout(() => initSession(accountId), 3000);
            } else {
                sessionStatus.set(accountId, 'disconnected');
                sessions.delete(accountId);
                // Clear session folder if logged out
                if (fs.existsSync(sessionPath)) {
                    fs.rmSync(sessionPath, { recursive: true, force: true });
                }
            }
        } else if (connection === 'open') {
            qrCodes.delete(accountId);
            sessionStatus.set(accountId, 'connected');
            console.log(`✅ WhatsApp Account #${accountId} successfully connected!`);
        }
    });

    // Handle incoming messages
    sock.ev.on('messages.upsert', async (m) => {
        if (m.type === 'notify') {
            for (const msg of m.messages) {
                if (!msg.key.fromMe && msg.message) {
                    console.log(`📩 Incoming WhatsApp msg on Account #${accountId} from ${msg.key.remoteJid}`);
                }
            }
        }
    });

    sessions.set(accountId, sock);
    return sock;
}

// -------------------------------------------------------------
// API Endpoints
// -------------------------------------------------------------

// 1. Health check
app.get('/health', (req, res) => {
    res.json({ status: 'ok', service: 'TravelWave WhatsApp Baileys Gateway' });
});

// 2. Get QR Code or status for an account
app.get('/qr/:accountId', async (req, res) => {
    const { accountId } = req.params;
    let status = sessionStatus.get(accountId) || 'disconnected';

    if (status === 'connected') {
        return res.json({
            status: 'connected',
            message: 'الرقم متصل بالفعل وتعمل الجلسة بنجاح.'
        });
    }

    if (!sessions.has(accountId) || status === 'disconnected') {
        initSession(accountId);
        await delay(1500);
        status = sessionStatus.get(accountId) || 'connecting';
    }

    const qrBase64 = qrCodes.get(accountId);

    if (qrBase64) {
        return res.json({
            status: 'qr_ready',
            qr: qrBase64,
            message: 'تم توليد QR Code حقيقي ومباشر لواتساب. يرجى مسحه من الموبايل.'
        });
    }

    return res.json({
        status: status,
        message: 'جاري تجهيز كود QR التفاعلي... يُرجى الانتظار لححظات أو إعادة المحاولة.'
    });
});

// 3. Get session status
app.get('/status/:accountId', (req, res) => {
    const { accountId } = req.params;
    const status = sessionStatus.get(accountId) || 'disconnected';
    res.json({ accountId, status });
});

// 4. Send Message Endpoint
app.post('/send', async (req, res) => {
    const { accountId, phone, message } = req.body;

    if (!accountId || !phone || !message) {
        return res.status(400).json({ error: 'Missing required parameters: accountId, phone, message' });
    }

    let sock = sessions.get(accountId);

    if (!sock || sessionStatus.get(accountId) !== 'connected') {
        return res.status(400).json({ error: `WhatsApp Account #${accountId} is not connected.` });
    }

    try {
        const cleanPhone = phone.replace(/\D/g, '');
        const jid = `${cleanPhone}@s.whatsapp.net`;

        const sent = await sock.sendMessage(jid, { text: message });

        return res.json({
            status: 'success',
            messageId: sent.key.id,
            timestamp: sent.messageTimestamp
        });
    } catch (err) {
        console.error(`Error sending message on Account #${accountId}:`, err);
        return res.status(500).json({ error: err.message });
    }
});

// 5. Disconnect / Logout
app.post('/disconnect/:accountId', async (req, res) => {
    const { accountId } = req.params;
    const sock = sessions.get(accountId);

    if (sock) {
        try {
            await sock.logout();
        } catch (e) {}
        sessions.delete(accountId);
        qrCodes.delete(accountId);
        sessionStatus.set(accountId, 'disconnected');
    }

    res.json({ status: 'disconnected', accountId });
});

// Start Server
app.listen(PORT, () => {
    console.log(`🚀 TravelWave WhatsApp Baileys Gateway running on port ${PORT}`);
});
