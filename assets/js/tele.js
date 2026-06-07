const token = "8680128600:AAH_uuititcIn83IEKFwOTAxUqrHBP-nrxw"
const group_id = "-4997587400" // Added '-' because Telegram group IDs are negative

function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

function sendTelegram(message, customChatId = null) {
    const url = `https://api.telegram.org/bot${token}/sendMessage`;
    const targetChatId = customChatId || group_id;
    const data = {
        chat_id: targetChatId,
        text: message,
        parse_mode: 'HTML'
    };

    console.log(`Sending to Telegram (${targetChatId})...`, data);

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(resData => {
            if (resData.ok) {
                console.log('Telegram OK:', resData);
            } else {
                console.error('Telegram API Error:', resData);
                // If it fails because of chat not found, it might be the ID
                if (resData.description && resData.description.includes('chat not found')) {
                    console.warn('Check if group_id is correct and Bot is a member of the group.');
                }
            }
        })
        .catch(err => console.error('Telegram Network/Fetch Error:', err));
}
