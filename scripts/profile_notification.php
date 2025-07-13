<?php
if (!function_exists('getProfileIncompleteInfo')) {
    require_once __DIR__ . '/functions.php';
}
$profileInfo = getProfileIncompleteInfo($user);
if ($profileInfo['incompleto']): ?>
<style>
.profile-notification {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    border-left: 5px solid #ff4757;
    position: relative;
    overflow: hidden;
}
.profile-notification h3 {margin:0 0 10px 0;font-size:18px;font-weight:600;display:flex;align-items:center;gap:10px;}
.profile-notification p {margin:0 0 15px 0;font-size:14px;opacity:0.9;}
.profile-notification .missing-fields {background:rgba(255,255,255,0.2);padding:10px;border-radius:8px;margin:10px 0;font-size:13px;}
.profile-notification .btn-complete-profile {background:rgba(255,255,255,0.2);color:white;border:2px solid rgba(255,255,255,0.3);padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;transition:all 0.3s ease;display:inline-flex;align-items:center;gap:8px;}
.profile-notification .btn-complete-profile:hover {background:rgba(255,255,255,0.3);transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,0.2);}
.profile-notification .close-notification {position:absolute;top:15px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;opacity:0.7;transition:opacity 0.3s ease;}
.profile-notification .close-notification:hover {opacity:1;}
</style>
<div class="profile-notification" id="profile-notification">
    <button class="close-notification" onclick="closeNotification()">&times;</button>
    <h3><i class="fas fa-exclamation-triangle"></i> ¡Completa tu perfil!</h3>
    <p>Para una mejor experiencia, necesitamos que completes algunos datos de tu perfil:</p>
    <div class="missing-fields"><strong>Campos faltantes:</strong> <?php echo implode(', ', $profileInfo['campos_faltantes']); ?></div>
    <a href="../models/perfil/perfil.php" class="btn-complete-profile"><i class="fas fa-user-edit"></i>Completar Perfil</a>
</div>
<script>
function closeNotification() {
    const notification = document.getElementById('profile-notification');
    if (notification) notification.style.display = 'none';
    localStorage.setItem('profileNotificationClosed', Date.now());
}
document.addEventListener('DOMContentLoaded', function() {
    const lastClosed = localStorage.getItem('profileNotificationClosed');
    if (lastClosed) {
        const timeDiff = Date.now() - parseInt(lastClosed);
        if (timeDiff < 24 * 60 * 60 * 1000) {
            const notification = document.getElementById('profile-notification');
            if (notification) notification.style.display = 'none';
        }
    }
});
</script>
<?php endif; ?> 