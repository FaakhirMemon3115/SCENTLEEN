<?php
// contact.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        // Simulate sending an email or saving to a database
        // For now, we will just show a success message
        $success = "Thank you for contacting us! Our team will get back to you within 24 hours.";
    }
}
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px; max-width: 1000px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 15px; color: var(--gold-color);">Contact Us</h1>
            <p style="color: #666; max-width: 600px; margin: 0 auto;">Have a question about our fragrances, your order, or just want to say hello? Drop us a message and our concierge team will assist you.</p>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 40px;">
            
            <!-- Contact Info -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); height: 100%;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 25px; color: var(--text-color);">Get in Touch</h3>
                    
                    <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px;">
                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-color);">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 5px;">Boutique Location</h4>
                            <p style="color: #666; font-size: 0.9rem; line-height: 1.5;">123 Luxury Avenue, Suite 45<br>Fragrance District, Paris 75008</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px;">
                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-color);">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 5px;">Phone</h4>
                            <p style="color: #666; font-size: 0.9rem;">+33 1 23 45 67 89</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 30px;">
                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-color);">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 5px;">Email</h4>
                            <p style="color: #666; font-size: 0.9rem;">support@scentleen.com</p>
                        </div>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
                    
                    <h4 style="font-size: 1rem; margin-bottom: 15px;">Follow Us</h4>
                    <div style="display: flex; gap: 15px;">
                        <a href="#" style="width: 40px; height: 40px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-color); transition: all 0.3s;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-color); transition: all 0.3s;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-color); transition: all 0.3s;"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div style="flex: 1.5; min-width: 300px;">
                <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 1.5rem; margin-bottom: 25px; color: var(--text-color);">Send a Message</h3>
                    
                    <?php if ($error): ?>
                        <div style="background: #fdeaea; color: #c0392b; padding: 15px; border-radius: 5px; margin-bottom: 25px; border-left: 4px solid #e74c3c;">
                            <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 25px; border-left: 4px solid #27ae60;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                <div style="flex: 1;">
                                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #444;">Name *</label>
                                    <input type="text" name="name" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #444;">Email *</label>
                                    <input type="email" name="email" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #444;">Subject</label>
                                <input type="text" name="subject" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                            </div>
                            
                            <div style="margin-bottom: 25px;">
                                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #444;">Message *</label>
                                <textarea name="message" rows="5" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); resize: vertical;"></textarea>
                            </div>
                            
                            <button type="submit" class="btn-primary" style="padding: 15px 30px; font-size: 1rem; cursor: pointer; border: none; border-radius: 5px;">Send Message</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</main>

<style>
    input:focus, textarea:focus { border-color: var(--gold-color) !important; box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.1); }
</style>

<?php require_once 'includes/footer.php'; ?>
