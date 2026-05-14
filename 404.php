<?php
http_response_code(404);
$currentPage = '404';
$pageTitle = 'Page Not Found';
include 'config.php';
include 'functions.php';

$pageDescription = 'The page you are looking for could not be found. Browse Lukman Primary School website for admissions, news, events, and more.';
$noIndex = true;
include 'includes/header.php';
?>

<section style="padding: 160px 0 100px; background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <!-- 404 Illustration -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 8rem; font-weight: 900; color: var(--primary-yellow); line-height: 1; text-shadow: 0 4px 20px rgba(0,0,0,0.2);">404</div>
                    <div style="width: 80px; height: 4px; background: var(--primary-yellow); margin: 1.5rem auto; border-radius: 2px;"></div>
                </div>
                
                <h1 style="color: #fff; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">
                    Page Not Found
                </h1>
                <p style="color: rgba(255,255,255,0.85); font-size: 1.15rem; line-height: 1.8; max-width: 550px; margin: 0 auto 2.5rem;">
                    Sorry, the page you are looking for doesn't exist or has been moved. Let us help you find what you need.
                </p>

                <!-- Search Box -->
                <div style="max-width: 480px; margin: 0 auto 2.5rem;">
                    <form action="news.php" method="get" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" placeholder="Search products, news, events..." 
                               style="flex: 1; padding: 0.85rem 1.25rem; border: 2px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #fff; border-radius: 8px; font-size: 1rem; outline: none; transition: border-color 0.3s;"
                               onfocus="this.style.borderColor='var(--primary-yellow)'" 
                               onblur="this.style.borderColor='rgba(255,255,255,0.2)'">
                        <button type="submit" style="padding: 0.85rem 1.5rem; background: var(--primary-yellow); color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Quick Navigation -->
                <div style="margin-bottom: 2rem;">
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.25rem; font-weight: 600;">Popular Pages</p>
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem;">
                        <a href="index.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-home"></i> Home
                        </a>
                        <a href="admissions.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-user-plus"></i> Admissions
                        </a>
                        <a href="about.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-info-circle"></i> About Us
                        </a>
                        <a href="contact.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                        <a href="events.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-calendar-alt"></i> Events
                        </a>
                        <a href="gallery.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-images"></i> Gallery
                        </a>
                        <a href="news.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-newspaper"></i> News
                        </a>
                        <a href="results.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 50px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-graduation-cap"></i> PLE Results
                        </a>
                    </div>
                </div>

                <!-- Go Home Button -->
                <a href="index.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2.5rem; background: var(--primary-yellow); color: #fff; border-radius: 8px; font-weight: 700; font-size: 1.05rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
