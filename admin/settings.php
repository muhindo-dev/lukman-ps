<?php
/**
 * Site Settings Management
 * Comprehensive settings for the entire Lukman PS website
 * 
 * @author Lukman PS Development Team
 * @version 2.0.0
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'settings';
$pageTitle = 'Site Settings';

$pdo = getDBConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $success = true;
    
    // Get all posted data
    $settings = $_POST['settings'] ?? [];
    
    // Handle logo upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $fileExt = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Invalid logo format. Allowed: JPG, PNG, SVG, WebP.';
        } elseif ($_FILES['site_logo']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Logo size must be less than 2MB.';
        } else {
            $uploadDir = '../uploads/site/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $newFileName = 'logo_' . time() . '.' . $fileExt;
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $uploadDir . $newFileName)) {
                $oldLogo = getSettingLocal($pdo, 'site_logo');
                if ($oldLogo && file_exists('../uploads/' . $oldLogo)) {
                    @unlink('../uploads/' . $oldLogo);
                }
                $settings['site_logo'] = 'site/' . $newFileName;
            }
        }
    }
    
    // Handle logo light upload (for dark backgrounds)
    if (isset($_FILES['site_logo_light']) && $_FILES['site_logo_light']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $fileExt = strtolower(pathinfo($_FILES['site_logo_light']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Invalid light logo format.';
        } elseif ($_FILES['site_logo_light']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Light logo size must be less than 2MB.';
        } else {
            $uploadDir = '../uploads/site/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $newFileName = 'logo_light_' . time() . '.' . $fileExt;
            if (move_uploaded_file($_FILES['site_logo_light']['tmp_name'], $uploadDir . $newFileName)) {
                $oldLogo = getSettingLocal($pdo, 'site_logo_light');
                if ($oldLogo && file_exists('../uploads/' . $oldLogo)) {
                    @unlink('../uploads/' . $oldLogo);
                }
                $settings['site_logo_light'] = 'site/' . $newFileName;
            }
        }
    }
    
    // Handle favicon upload
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['ico', 'png'];
        $fileExt = strtolower(pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Invalid favicon format. Allowed: ICO, PNG.';
        } elseif ($_FILES['site_favicon']['size'] > 500 * 1024) {
            $errors[] = 'Favicon size must be less than 500KB.';
        } else {
            $uploadDir = '../uploads/site/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $newFileName = 'favicon_' . time() . '.' . $fileExt;
            if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $uploadDir . $newFileName)) {
                $oldFavicon = getSettingLocal($pdo, 'site_favicon');
                if ($oldFavicon && file_exists('../uploads/' . $oldFavicon)) {
                    @unlink('../uploads/' . $oldFavicon);
                }
                $settings['site_favicon'] = 'site/' . $newFileName;
            }
        }
    }
    
    // Handle OG Image upload
    if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $fileExt = strtolower(pathinfo($_FILES['og_image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Invalid OG image format. Allowed: JPG, PNG.';
        } elseif ($_FILES['og_image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'OG image size must be less than 2MB.';
        } else {
            $uploadDir = '../uploads/site/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $newFileName = 'og_image_' . time() . '.' . $fileExt;
            if (move_uploaded_file($_FILES['og_image']['tmp_name'], $uploadDir . $newFileName)) {
                $oldOG = getSettingLocal($pdo, 'og_image');
                if ($oldOG && file_exists('../uploads/' . $oldOG)) {
                    @unlink('../uploads/' . $oldOG);
                }
                $settings['og_image'] = 'site/' . $newFileName;
            }
        }
    }
    
    // Process checkbox fields (unchecked = not sent)
    $checkboxFields = ['maintenance_mode', 'show_donation_popup', 'enable_whatsapp_chat', 'enable_google_analytics'];
    foreach ($checkboxFields as $field) {
        if (!isset($settings[$field])) {
            $settings[$field] = '0';
        }
    }
    
    // Update all settings
    if (empty($errors)) {
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("
                INSERT INTO site_settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
            ");
            if (!$stmt->execute([$key, $value, $value])) {
                $success = false;
            }
        }
        
        if ($success) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Settings updated successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to update some settings.'];
        }
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
    }
    
    header('Location: settings.php');
    exit;
}

// Local helper function for getting settings
function getSettingLocal($pdo, $key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $cache = [];
        while ($row = $stmt->fetch()) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

// Load settings for display
$settingsCache = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settingsCache[$row['setting_key']] = $row['setting_value'];
}

// Helper for template
function s($key, $default = '') {
    global $settingsCache;
    return $settingsCache[$key] ?? $default;
}

include 'includes/header.php';
?>

<div class="admin-content">
    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
            <i class="fas fa-<?php echo $_SESSION['alert']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $_SESSION['alert']['message']; ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
    
    <div class="content-header-compact">
        <h1><i class="fas fa-cog"></i> Site Settings</h1>
    </div>

    <!-- Settings Navigation Tabs -->
    <div class="settings-tabs">
        <button class="tab-btn active" data-tab="general"><i class="fas fa-info-circle"></i> <span>General</span></button>
        <button class="tab-btn" data-tab="contact"><i class="fas fa-address-book"></i> <span>Contact</span></button>
        <button class="tab-btn" data-tab="branding"><i class="fas fa-palette"></i> <span>Branding</span></button>
        <button class="tab-btn" data-tab="social"><i class="fas fa-share-alt"></i> <span>Social</span></button>
        <button class="tab-btn" data-tab="payment"><i class="fas fa-credit-card"></i> <span>Payment</span></button>
        <button class="tab-btn" data-tab="seo"><i class="fas fa-search"></i> <span>SEO</span></button>
        <button class="tab-btn" data-tab="advanced"><i class="fas fa-tools"></i> <span>Advanced</span></button>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" id="settings-form">
        
        <!-- General Settings Tab -->
        <div class="tab-content active" id="tab-general">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-building"></i> Organization Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="site_name">Organization Name <span class="required">*</span></label>
                            <input type="text" id="site_name" name="settings[site_name]" class="form-control"
                                value="<?php echo htmlspecialchars(s('site_name', 'Lukman Primary School')); ?>" required>
                            <small class="form-text">Full name of your organization</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_short_name">Short Name / Acronym</label>
                            <input type="text" id="site_short_name" name="settings[site_short_name]" class="form-control"
                                value="<?php echo htmlspecialchars(s('site_short_name', 'Lukman PS')); ?>"
                                placeholder="e.g., Lukman PS">
                            <small class="form-text">Used in the header and favicon alt text</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_tagline">Tagline / Motto</label>
                            <input type="text" id="site_tagline" name="settings[site_tagline]" class="form-control"
                                value="<?php echo htmlspecialchars(s('site_tagline', 'Excellence in Islamic & UNC Education')); ?>"
                                placeholder="Your inspiring tagline">
                            <small class="form-text">Appears below the logo</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_description">About / Description</label>
                            <textarea id="site_description" name="settings[site_description]" class="form-control" rows="4"
                                placeholder="Brief description of your organization"><?php echo htmlspecialchars(s('site_description')); ?></textarea>
                            <small class="form-text">Used in meta tags and footer</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="founding_year">Year Founded</label>
                                <input type="number" id="founding_year" name="settings[founding_year]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('founding_year')); ?>"
                                    placeholder="e.g., 2010" min="1900" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="registration_number">Registration Number</label>
                                <input type="text" id="registration_number" name="settings[registration_number]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('registration_number')); ?>"
                                    placeholder="NGO registration number">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-bullseye"></i> Mission & Vision</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="mission_statement">Mission Statement</label>
                            <textarea id="mission_statement" name="settings[mission_statement]" class="form-control" rows="3"
                                placeholder="Our mission is to..."><?php echo htmlspecialchars(s('mission_statement')); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="vision_statement">Vision Statement</label>
                            <textarea id="vision_statement" name="settings[vision_statement]" class="form-control" rows="3"
                                placeholder="Our vision is to..."><?php echo htmlspecialchars(s('vision_statement')); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill"></i> Currency Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="currency_code">Currency Code</label>
                                <select id="currency_code" name="settings[currency_code]" class="form-control">
                                    <option value="UGX" <?php echo s('currency_code', 'UGX') == 'UGX' ? 'selected' : ''; ?>>UGX - Ugandan Shilling</option>
                                    <option value="USD" <?php echo s('currency_code') == 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                    <option value="KES" <?php echo s('currency_code') == 'KES' ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                                    <option value="TZS" <?php echo s('currency_code') == 'TZS' ? 'selected' : ''; ?>>TZS - Tanzanian Shilling</option>
                                    <option value="EUR" <?php echo s('currency_code') == 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                    <option value="GBP" <?php echo s('currency_code') == 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="currency_symbol">Display Symbol</label>
                                <input type="text" id="currency_symbol" name="settings[currency_symbol]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('currency_symbol', 'UGX')); ?>"
                                    placeholder="UGX" maxlength="10">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="min_donation">Minimum Donation Amount (USD)</label>
                            <input type="number" id="min_donation" name="settings[min_donation]" class="form-control"
                                value="<?php echo htmlspecialchars(s('min_donation', '5')); ?>"
                                placeholder="5" min="1">
                            <small class="form-text">Minimum amount allowed for donations in USD</small>
                        </div>
                        <div class="form-group">
                            <label for="usd_to_ugx_rate">USD to UGX Exchange Rate</label>
                            <input type="number" id="usd_to_ugx_rate" name="settings[usd_to_ugx_rate]" class="form-control"
                                value="<?php echo htmlspecialchars(s('usd_to_ugx_rate', '3600')); ?>"
                                placeholder="3600" min="1" step="1">
                            <small class="form-text">1 USD = X UGX (Pesapal processes payments in UGX)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Settings Tab -->
        <div class="tab-content" id="tab-contact">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-phone-alt"></i> Primary Contact</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="site_email">Organization Email</label>
                            <input type="email" id="site_email" name="settings[site_email]" class="form-control"
                                value="<?php echo htmlspecialchars(s('site_email')); ?>"
                                placeholder="info@lukmanps.ac.ug">
                            <small class="form-text">Primary organization email</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_phone">Organization Phone</label>
                            <input type="text" id="site_phone" name="settings[site_phone]" class="form-control"
                                value="<?php echo htmlspecialchars(s('site_phone')); ?>"
                                placeholder="+256 700 000 000">
                            <small class="form-text">Primary organization phone with country code</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="site_address">Physical Address</label>
                            <textarea id="site_address" name="settings[site_address]" class="form-control" rows="2"
                                placeholder="Kitoro Zone, Entebbe, Wakiso District, Uganda"><?php echo htmlspecialchars(s('site_address')); ?></textarea>
                            <small class="form-text">Physical location address</small>
                        </div>
                        
                        <hr style="margin: 1.5rem 0;">
                        <h4 style="font-size: 0.95rem; margin-bottom: 1rem; color: #666;">Contact Page Settings</h4>
                        
                        <div class="form-group">
                            <label for="contact_email">Display Email</label>
                            <input type="email" id="contact_email" name="settings[contact_email]" class="form-control"
                                value="<?php echo htmlspecialchars(s('contact_email')); ?>"
                                placeholder="info@lukmanps.ac.ug">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone">Display Phone</label>
                            <input type="text" id="contact_phone" name="settings[contact_phone]" class="form-control"
                                value="<?php echo htmlspecialchars(s('contact_phone')); ?>"
                                placeholder="+256 700 000 000">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone_alt">Alternative Phone</label>
                            <input type="text" id="contact_phone_alt" name="settings[contact_phone_alt]" class="form-control"
                                value="<?php echo htmlspecialchars(s('contact_phone_alt')); ?>"
                                placeholder="+256 700 000 001">
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp_number">WhatsApp Number</label>
                            <input type="text" id="whatsapp_number" name="settings[whatsapp_number]" class="form-control"
                                value="<?php echo htmlspecialchars(s('whatsapp_number')); ?>"
                                placeholder="+256757689986">
                            <small class="form-text">Without spaces or dashes. Used for WhatsApp chat button.</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-map-marker-alt"></i> Location</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="contact_address">Postal Address</label>
                            <textarea id="contact_address" name="settings[contact_address]" class="form-control" rows="2"
                                placeholder="P.O. Box 400, Entebbe"><?php echo htmlspecialchars(s('contact_address')); ?></textarea>
                            <small class="form-text">Postal/mailing address</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_city">City/Town</label>
                                <input type="text" id="contact_city" name="settings[contact_city]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('contact_city')); ?>"
                                    placeholder="Entebbe">
                            </div>
                            <div class="form-group">
                                <label for="contact_country">Country</label>
                                <input type="text" id="contact_country" name="settings[contact_country]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('contact_country', 'Uganda')); ?>"
                                    placeholder="Uganda">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="google_maps_embed">Google Maps Embed Code</label>
                            <textarea id="google_maps_embed" name="settings[google_maps_embed]" class="form-control" rows="3"
                                placeholder="<iframe src='...'></iframe>"><?php echo htmlspecialchars(s('google_maps_embed')); ?></textarea>
                            <small class="form-text">Paste the full iframe embed code from Google Maps</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-clock"></i> Office Hours</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="office_hours">Working Hours</label>
                            <input type="text" id="office_hours" name="settings[office_hours]" class="form-control"
                                value="<?php echo htmlspecialchars(s('office_hours')); ?>"
                                placeholder="Mon - Fri: 8:00 AM - 5:00 PM">
                        </div>
                        
                        <div class="form-group">
                            <label for="office_hours_weekend">Weekend Hours</label>
                            <input type="text" id="office_hours_weekend" name="settings[office_hours_weekend]" class="form-control"
                                value="<?php echo htmlspecialchars(s('office_hours_weekend')); ?>"
                                placeholder="Sat: 9:00 AM - 1:00 PM">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branding Settings Tab -->
        <div class="tab-content" id="tab-branding">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-image"></i> Logo</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Primary Logo (Dark)</label>
                            <?php if (s('site_logo')): ?>
                                <div class="logo-preview">
                                    <img src="../uploads/<?php echo htmlspecialchars(s('site_logo')); ?>" alt="Logo">
                                </div>
                            <?php else: ?>
                                <div class="logo-preview empty">
                                    <i class="fas fa-image"></i>
                                    <span>No logo uploaded</span>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="site_logo" name="site_logo" class="form-control" accept="image/*">
                            <small class="form-text">For light backgrounds. JPG, PNG, SVG, WebP. Max 2MB. Recommended: 250x80px</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Light Logo (for dark backgrounds)</label>
                            <?php if (s('site_logo_light')): ?>
                                <div class="logo-preview dark-bg">
                                    <img src="../uploads/<?php echo htmlspecialchars(s('site_logo_light')); ?>" alt="Light Logo">
                                </div>
                            <?php else: ?>
                                <div class="logo-preview empty dark-bg">
                                    <i class="fas fa-image"></i>
                                    <span>No light logo</span>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="site_logo_light" name="site_logo_light" class="form-control" accept="image/*">
                            <small class="form-text">Optional. For footer and dark sections.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo_icon_class">Logo Icon Class (Fallback)</label>
                            <input type="text" id="logo_icon_class" name="settings[logo_icon_class]" class="form-control"
                                value="<?php echo htmlspecialchars(s('logo_icon_class', 'fas fa-school')); ?>"
                                placeholder="fas fa-school">
                            <small class="form-text">Font Awesome icon class used when no logo image</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-star"></i> Favicon</h3>
                    </div>
                    <div class="card-body">
                        <?php if (s('site_favicon')): ?>
                            <div class="favicon-preview">
                                <img src="../uploads/<?php echo htmlspecialchars(s('site_favicon')); ?>" alt="Favicon">
                            </div>
                        <?php else: ?>
                            <div class="favicon-preview empty">
                                <i class="fas fa-star"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="site_favicon" name="site_favicon" class="form-control" accept=".ico,.png">
                        <small class="form-text">ICO or PNG. 32x32 or 64x64 pixels. Max 500KB.</small>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-paint-brush"></i> Colors</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primary_color">Primary Color</label>
                                <input type="color" id="primary_color" name="settings[primary_color]" class="form-control-color"
                                    value="<?php echo htmlspecialchars(s('primary_color', '#FFC107')); ?>">
                                <small class="form-text"><?php echo s('primary_color', '#FFC107'); ?></small>
                            </div>
                            <div class="form-group">
                                <label for="secondary_color">Secondary Color</label>
                                <input type="color" id="secondary_color" name="settings[secondary_color]" class="form-control-color"
                                    value="<?php echo htmlspecialchars(s('secondary_color', '#1A1A1A')); ?>">
                                <small class="form-text"><?php echo s('secondary_color', '#1A1A1A'); ?></small>
                            </div>
                        </div>
                        <div class="alert alert-info" style="margin-top: 1rem;">
                            <i class="fas fa-info-circle"></i> Color customization will be fully applied in a future update.
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-copyright"></i> Footer</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="footer_text">Footer Copyright Text</label>
                            <input type="text" id="footer_text" name="settings[footer_text]" class="form-control"
                                value="<?php echo htmlspecialchars(s('footer_text')); ?>"
                                placeholder="All rights reserved.">
                            <small class="form-text">Year and organization name are added automatically</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="footer_about">Footer About Text</label>
                            <textarea id="footer_about" name="settings[footer_about]" class="form-control" rows="3"
                                placeholder="Brief description for footer"><?php echo htmlspecialchars(s('footer_about')); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="developer_name">Developer Credit</label>
                            <input type="text" id="developer_name" name="settings[developer_name]" class="form-control"
                                value="<?php echo htmlspecialchars(s('developer_name')); ?>"
                                placeholder="Developer name">
                        </div>
                        
                        <div class="form-group">
                            <label for="developer_url">Developer URL</label>
                            <input type="url" id="developer_url" name="settings[developer_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('developer_url')); ?>"
                                placeholder="https://developer-website.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Tab -->
        <div class="tab-content" id="tab-social">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-share-alt"></i> Social Media Links</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="facebook_url"><i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook Page</label>
                            <input type="url" id="facebook_url" name="settings[facebook_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('facebook_url')); ?>"
                                placeholder="https://facebook.com/yourpage">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter_url"><i class="fab fa-twitter" style="color: #1DA1F2;"></i> Twitter / X</label>
                            <input type="url" id="twitter_url" name="settings[twitter_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('twitter_url')); ?>"
                                placeholder="https://twitter.com/yourhandle">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram_url"><i class="fab fa-instagram" style="color: #E4405F;"></i> Instagram</label>
                            <input type="url" id="instagram_url" name="settings[instagram_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('instagram_url')); ?>"
                                placeholder="https://instagram.com/yourhandle">
                        </div>
                        
                        <div class="form-group">
                            <label for="youtube_url"><i class="fab fa-youtube" style="color: #FF0000;"></i> YouTube Channel</label>
                            <input type="url" id="youtube_url" name="settings[youtube_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('youtube_url')); ?>"
                                placeholder="https://youtube.com/@yourchannel">
                        </div>
                        
                        <div class="form-group">
                            <label for="linkedin_url"><i class="fab fa-linkedin" style="color: #0A66C2;"></i> LinkedIn</label>
                            <input type="url" id="linkedin_url" name="settings[linkedin_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('linkedin_url')); ?>"
                                placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                        
                        <div class="form-group">
                            <label for="tiktok_url"><i class="fab fa-tiktok" style="color: #000;"></i> TikTok</label>
                            <input type="url" id="tiktok_url" name="settings[tiktok_url]" class="form-control"
                                value="<?php echo htmlspecialchars(s('tiktok_url')); ?>"
                                placeholder="https://tiktok.com/@yourhandle">
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fab fa-whatsapp"></i> WhatsApp Integration</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="settings[enable_whatsapp_chat]" value="1"
                                    <?php echo (s('enable_whatsapp_chat') == '1') ? 'checked' : ''; ?>>
                                <span>Enable WhatsApp Chat Button</span>
                            </label>
                            <small class="form-text">Show floating WhatsApp button on all pages</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp_default_message">Default Message</label>
                            <textarea id="whatsapp_default_message" name="settings[whatsapp_default_message]" class="form-control" rows="2"
                                placeholder="Hello, I would like to know more about Lukman Primary School."><?php echo htmlspecialchars(s('whatsapp_default_message', 'Hello, I would like to know more about Lukman Primary School.')); ?></textarea>
                            <small class="form-text">Pre-filled message when visitors click WhatsApp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Settings Tab -->
        <div class="tab-content" id="tab-payment">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-credit-card"></i> Pesapal Payment Gateway</h3>
                    </div>
                    <div class="card-body">
                        <div class="pesapal-notice">
                            <i class="fas fa-info-circle"></i>
                            Get credentials from <a href="https://www.pesapal.com/dashboard" target="_blank">Pesapal Dashboard</a>. 
                            Register IPN at <a href="https://pay.pesapal.com/iframe/PesapalIframe3/IpnRegistration" target="_blank">Pesapal IPN Registration</a>.
                        </div>
                        
                        <div class="form-group">
                            <label for="pesapal_environment">Environment <span class="required">*</span></label>
                            <select id="pesapal_environment" name="settings[pesapal_environment]" class="form-control">
                                <option value="sandbox" <?php echo (s('pesapal_environment', 'sandbox') == 'sandbox') ? 'selected' : ''; ?>>Sandbox (Testing)</option>
                                <option value="live" <?php echo (s('pesapal_environment') == 'live') ? 'selected' : ''; ?>>Live (Production)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="pesapal_consumer_key">Consumer Key <span class="required">*</span></label>
                            <input type="text" id="pesapal_consumer_key" name="settings[pesapal_consumer_key]" class="form-control"
                                value="<?php echo htmlspecialchars(s('pesapal_consumer_key')); ?>"
                                placeholder="Your Pesapal Consumer Key">
                        </div>
                        
                        <div class="form-group">
                            <label for="pesapal_consumer_secret">Consumer Secret <span class="required">*</span></label>
                            <div class="password-field">
                                <input type="password" id="pesapal_consumer_secret" name="settings[pesapal_consumer_secret]" class="form-control"
                                    value="<?php echo htmlspecialchars(s('pesapal_consumer_secret')); ?>"
                                    placeholder="Your Pesapal Consumer Secret">
                                <button type="button" class="btn-toggle-password" onclick="togglePassword('pesapal_consumer_secret')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="pesapal_ipn_id">IPN ID <span class="required">*</span></label>
                            <input type="text" id="pesapal_ipn_id" name="settings[pesapal_ipn_id]" class="form-control"
                                value="<?php echo htmlspecialchars(s('pesapal_ipn_id')); ?>"
                                placeholder="IPN Notification ID">
                        </div>
                        
                        <div class="ipn-url-display">
                            <label>Your IPN URL:</label>
                            <code id="ipn-url"><?php 
                                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                                $host = $_SERVER['HTTP_HOST'];
                                $basePath = dirname($_SERVER['REQUEST_URI']);
                                $basePath = str_replace('/admin', '', $basePath);
                                $ipnUrl = $protocol . '://' . $host . $basePath . '/donation-ipn.php';
                                echo $ipnUrl;
                            ?></code>
                            <button type="button" class="btn-copy" onclick="copyToClipboard(document.getElementById('ipn-url').textContent)">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-university"></i> Bank Details (for manual transfers)</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" id="bank_name" name="settings[bank_name]" class="form-control"
                                value="<?php echo htmlspecialchars(s('bank_name')); ?>"
                                placeholder="e.g., Centenary Bank">
                        </div>
                        
                        <div class="form-group">
                            <label for="bank_account_name">Account Name</label>
                            <input type="text" id="bank_account_name" name="settings[bank_account_name]" class="form-control"
                                value="<?php echo htmlspecialchars(s('bank_account_name')); ?>"
                                placeholder="Account holder name">
                        </div>
                        
                        <div class="form-group">
                            <label for="bank_account_number">Account Number</label>
                            <input type="text" id="bank_account_number" name="settings[bank_account_number]" class="form-control"
                                value="<?php echo htmlspecialchars(s('bank_account_number')); ?>"
                                placeholder="Account number">
                        </div>
                        
                        <div class="form-group">
                            <label for="bank_swift_code">SWIFT/BIC Code</label>
                            <input type="text" id="bank_swift_code" name="settings[bank_swift_code]" class="form-control"
                                value="<?php echo htmlspecialchars(s('bank_swift_code')); ?>"
                                placeholder="For international transfers">
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile_money_number">Mobile Money Number</label>
                            <input type="text" id="mobile_money_number" name="settings[mobile_money_number]" class="form-control"
                                value="<?php echo htmlspecialchars(s('mobile_money_number')); ?>"
                                placeholder="e.g., +256 700 000 000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Settings Tab -->
        <div class="tab-content" id="tab-seo">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-search"></i> Search Engine Optimization</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="meta_title">Default Page Title</label>
                            <input type="text" id="meta_title" name="settings[meta_title]" class="form-control"
                                value="<?php echo htmlspecialchars(s('meta_title')); ?>"
                                placeholder="Lukman Primary School - Official Website">
                            <small class="form-text">Used when page-specific title is not set</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">Default Meta Description</label>
                            <textarea id="meta_description" name="settings[meta_description]" class="form-control" rows="3"
                                placeholder="Compelling description for search results (150-160 characters)"><?php echo htmlspecialchars(s('meta_description')); ?></textarea>
                            <small class="form-text"><span id="meta-desc-count">0</span>/160 characters recommended</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" id="meta_keywords" name="settings[meta_keywords]" class="form-control"
                                value="<?php echo htmlspecialchars(s('meta_keywords')); ?>"
                                placeholder="primary school, Entebbe, Uganda, Islamic education, boarding school">
                            <small class="form-text">Comma-separated keywords</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fab fa-facebook"></i> Social Sharing (Open Graph)</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="og_title">OG Title</label>
                            <input type="text" id="og_title" name="settings[og_title]" class="form-control"
                                value="<?php echo htmlspecialchars(s('og_title')); ?>"
                                placeholder="Title for social media shares">
                        </div>
                        
                        <div class="form-group">
                            <label for="og_description">OG Description</label>
                            <textarea id="og_description" name="settings[og_description]" class="form-control" rows="2"
                                placeholder="Description for social media shares"><?php echo htmlspecialchars(s('og_description')); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>OG Image (1200x630px recommended)</label>
                            <?php if (s('og_image')): ?>
                                <div class="og-preview">
                                    <img src="../uploads/<?php echo htmlspecialchars(s('og_image')); ?>" alt="OG Image">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="og_image" name="og_image" class="form-control" accept="image/jpeg,image/png">
                            <small class="form-text">Image shown when page is shared on Facebook/Twitter</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-bar"></i> Analytics</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="settings[enable_google_analytics]" value="1"
                                    <?php echo (s('enable_google_analytics') == '1') ? 'checked' : ''; ?>>
                                <span>Enable Google Analytics</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label for="google_analytics_id">Google Analytics ID</label>
                            <input type="text" id="google_analytics_id" name="settings[google_analytics_id]" class="form-control"
                                value="<?php echo htmlspecialchars(s('google_analytics_id')); ?>"
                                placeholder="G-XXXXXXXXXX or UA-XXXXXXXX-X">
                        </div>
                        
                        <div class="form-group">
                            <label for="facebook_pixel_id">Facebook Pixel ID</label>
                            <input type="text" id="facebook_pixel_id" name="settings[facebook_pixel_id]" class="form-control"
                                value="<?php echo htmlspecialchars(s('facebook_pixel_id')); ?>"
                                placeholder="XXXXXXXXXXXXXXXXX">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div class="tab-content" id="tab-advanced">
            <div class="settings-grid">
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-shield-alt"></i> Site Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="checkbox-label warning">
                                <input type="checkbox" name="settings[maintenance_mode]" value="1"
                                    <?php echo (s('maintenance_mode') == '1') ? 'checked' : ''; ?>>
                                <span>Maintenance Mode</span>
                            </label>
                            <small class="form-text">When enabled, visitors see a maintenance page</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="maintenance_message">Maintenance Message</label>
                            <textarea id="maintenance_message" name="settings[maintenance_message]" class="form-control" rows="3"
                                placeholder="We are currently performing maintenance. Please check back soon."><?php echo htmlspecialchars(s('maintenance_message')); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-bell"></i> Notifications</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="notification_email">Admin Notification Email</label>
                            <input type="email" id="notification_email" name="settings[notification_email]" class="form-control"
                                value="<?php echo htmlspecialchars(s('notification_email')); ?>"
                                placeholder="admin@example.com">
                            <small class="form-text">Receives notifications for inquiries, donations, etc.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="settings[show_donation_popup]" value="1"
                                    <?php echo (s('show_donation_popup') == '1') ? 'checked' : ''; ?>>
                                <span>Show Donation Popup</span>
                            </label>
                            <small class="form-text">Display donation popup to visitors</small>
                        </div>
                    </div>
                </div>

                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-code"></i> Custom Code</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="custom_head_code">Custom Head Code</label>
                            <textarea id="custom_head_code" name="settings[custom_head_code]" class="form-control code-input" rows="4"
                                placeholder="<!-- Custom CSS, meta tags, etc. -->"><?php echo htmlspecialchars(s('custom_head_code')); ?></textarea>
                            <small class="form-text">Inserted before &lt;/head&gt;</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="custom_footer_code">Custom Footer Code</label>
                            <textarea id="custom_footer_code" name="settings[custom_footer_code]" class="form-control code-input" rows="4"
                                placeholder="<!-- Custom scripts, tracking codes, etc. -->"><?php echo htmlspecialchars(s('custom_footer_code')); ?></textarea>
                            <small class="form-text">Inserted before &lt;/body&gt;</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button (Fixed) -->
        <div class="save-bar">
            <button type="submit" class="btn-primary btn-save">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

<style>
.settings-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.tab-btn {
    padding: 0.6rem 1rem;
    border: 2px solid transparent;
    background: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    border-radius: 0;
}

.tab-btn:hover {
    color: #000;
    background: #f8f9fa;
}

.tab-btn.active {
    color: #000;
    border-color: #FFC107;
    background: #FFF9E6;
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 1.25rem;
}

.card {
    background: #fff;
    border: 2px solid #000;
}

.card-compact .card-header {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-bottom: 2px solid #000;
}

.card-compact .card-header h3 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-compact .card-body {
    padding: 1.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.4rem;
    font-size: 0.85rem;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.6rem 0.75rem;
    font-size: 0.9rem;
    border: 2px solid #dee2e6;
    outline: none;
    font-family: inherit;
    border-radius: 0;
    transition: border-color 0.2s;
}

.form-control:focus {
    border-color: #FFC107;
}

.form-control-color {
    width: 60px;
    height: 40px;
    padding: 0.25rem;
    cursor: pointer;
    border: 2px solid #dee2e6;
}

textarea.form-control {
    resize: vertical;
    min-height: 70px;
}

.code-input {
    font-family: 'Monaco', 'Consolas', 'Courier New', monospace;
    font-size: 0.8rem;
    background: #f8f9fa;
}

.form-text {
    display: block;
    margin-top: 0.35rem;
    font-size: 0.75rem;
    color: #6c757d;
}

.required {
    color: #dc3545;
}

.logo-preview {
    width: 100%;
    height: 90px;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
    overflow: hidden;
    padding: 0.5rem;
}

.logo-preview.dark-bg {
    background: #1a1a1a;
}

.logo-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.logo-preview.empty {
    flex-direction: column;
    gap: 0.35rem;
    color: #aaa;
}

.logo-preview.empty i {
    font-size: 1.75rem;
}

.logo-preview.empty span {
    font-size: 0.75rem;
}

.favicon-preview {
    width: 50px;
    height: 50px;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
}

.favicon-preview img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.favicon-preview.empty {
    color: #ccc;
    font-size: 1.25rem;
}

.og-preview {
    width: 100%;
    max-width: 300px;
    border: 2px solid #dee2e6;
    margin-bottom: 0.75rem;
    overflow: hidden;
}

.og-preview img {
    width: 100%;
    height: auto;
    display: block;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 600;
}

.checkbox-label.warning span {
    color: #856404;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #FFC107;
}

.password-field {
    position: relative;
    display: flex;
}

.password-field .form-control {
    padding-right: 40px;
}

.btn-toggle-password {
    position: absolute;
    right: 2px;
    top: 2px;
    bottom: 2px;
    width: 36px;
    background: #f8f9fa;
    border: none;
    cursor: pointer;
    color: #666;
}

.btn-toggle-password:hover {
    color: #000;
}

.pesapal-notice {
    background: #e7f3ff;
    border: 1px solid #b3d7ff;
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
    font-size: 0.85rem;
    border-radius: 0;
}

.pesapal-notice i {
    color: #0066cc;
    margin-right: 0.5rem;
}

.pesapal-notice a {
    color: #0066cc;
    text-decoration: underline;
}

.ipn-url-display {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    padding: 0.75rem 1rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.ipn-url-display label {
    font-size: 0.75rem;
    color: #666;
    margin: 0;
    font-weight: 600;
}

.ipn-url-display code {
    font-size: 0.8rem;
    background: #fff;
    padding: 0.35rem 0.5rem;
    border: 1px solid #ddd;
    flex: 1;
    word-break: break-all;
    font-family: 'Monaco', 'Consolas', monospace;
}

.btn-copy {
    background: #FFC107;
    border: 2px solid #000;
    padding: 0.35rem 0.75rem;
    cursor: pointer;
    font-size: 0.85rem;
    transition: background 0.2s;
}

.btn-copy:hover {
    background: #e0a800;
}

.save-bar {
    position: sticky;
    bottom: 0;
    background: #fff;
    border-top: 2px solid #000;
    padding: 1rem 1.5rem;
    margin: 1.5rem -1.5rem -1.5rem;
    text-align: center;
    z-index: 100;
}

.btn-save {
    padding: 0.85rem 2.5rem;
    font-size: 1rem;
    border: 2px solid #000;
    background: #FFC107;
    color: #000;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-save:hover {
    background: #e0a800;
    transform: translateY(-1px);
}

.btn-save:active {
    transform: translateY(0);
}

.alert {
    padding: 0.85rem 1rem;
    margin-bottom: 1.25rem;
    border: 2px solid;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert-info {
    background: #d1ecf1;
    border-color: #17a2b8;
    color: #0c5460;
    font-size: 0.8rem;
    padding: 0.65rem 0.75rem;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .settings-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 0.75rem;
    }
    
    .tab-btn {
        white-space: nowrap;
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
    
    .tab-btn span {
        display: none;
    }
    
    .save-bar {
        margin: 1rem -1rem -1rem;
        padding: 0.75rem 1rem;
    }
}
</style>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active from all
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active to clicked
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        
        // Save active tab to sessionStorage
        sessionStorage.setItem('activeSettingsTab', this.dataset.tab);
    });
});

// Restore active tab on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('activeSettingsTab');
    if (savedTab) {
        const tabBtn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
        if (tabBtn) {
            tabBtn.click();
        }
    }
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const btn = field.nextElementSibling || field.parentElement.querySelector('.btn-toggle-password');
    if (field.type === 'password') {
        field.type = 'text';
        if (btn) btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        field.type = 'password';
        if (btn) btn.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }).catch(function() {
        // Fallback
        const temp = document.createElement('textarea');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert('Copied to clipboard!');
    });
}

// Meta description character count
const metaDesc = document.getElementById('meta_description');
const metaCount = document.getElementById('meta-desc-count');
if (metaDesc && metaCount) {
    metaDesc.addEventListener('input', function() {
        metaCount.textContent = this.value.length;
        metaCount.style.color = this.value.length > 160 ? '#dc3545' : '#6c757d';
    });
    // Initialize count
    metaCount.textContent = metaDesc.value.length;
}

// Form submission confirmation for maintenance mode
document.getElementById('settings-form').addEventListener('submit', function(e) {
    const maintenanceCheckbox = document.querySelector('input[name="settings[maintenance_mode]"]');
    if (maintenanceCheckbox && maintenanceCheckbox.checked) {
        if (!confirm('Warning: Maintenance mode is enabled. Visitors will see a maintenance page. Continue?')) {
            e.preventDefault();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
