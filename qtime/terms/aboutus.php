<?php

require '../head.php';

$title = "About Us – QTime";
$company_name = "QTime ";
$founder = "Chong Kim Seng";
$year_founded = 2025;
$slogan = "Malaysian Craftsmanship, Timeless Elegance. Cherish Every Moment, Embrace the Beauty of Time.";
$hq_address = " QTime Sdn. Bhd.123, Jalan Teknologi 5, Taman Teknologi Malaysia, 57000 Kuala Lumpur, Malaysia.";
$phone = "+60 19-565 0721";
$email = "qtime@gmail.com.my";
$website = "www.qtimewatches.com.my";
$instagram = "qtime.kl";
$facebook = "QTime ";

$imageDir = "../aboutus/";

$images = [];
if (is_dir($imageDir)) {
    $files = scandir($imageDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($imageDir . $file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $images[] = $file;
            }
        }
    }
}

if (empty($images)) {
    $images[] = 'placeholder.jpg';
    if (!is_file($imageDir . 'placeholder.jpg')) {
        file_put_contents($imageDir . 'placeholder.jpg', file_get_contents('https://via.placeholder.com/800x400?text=No+Images+Found'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../css/aboutus.css">
</head>
<body>
    <header>
        <h1><?php ?></h1>
    </header>

    <main>
        <section class="slideshow">
            <div class="slideshow-container">
                <?php foreach ($images as $index => $image): ?>
                    <div class="slide">
                        <img src="<?php echo $imageDir . $image; ?>" alt="QTime Image <?php echo $index + 1; ?>">
                    </div>
                <?php endforeach; ?>
                
                <button class="slideshow-nav prev">&#10094;</button>
                <button class="slideshow-nav next">&#10095;</button>
            </div>

            <div class="slideshow-dots">
                <?php for ($i = 0; $i < count($images); $i++): ?>
                    <span class="dot" data-index="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            </div>
        </section>

        <section class="content-section">
            <h2>Background</h2>
            <p><?php echo $company_name; ?> is a watch brand originating from Malaysia, founded in <?php echo $year_founded; ?>. We take pride in local manufacturing and are committed to combining Malaysia's exquisite craftsmanship with international design concepts. Our goal is to create high-quality timepieces that showcase local characteristics while appealing to global aesthetics.</p>
            <p>The name "<strong>QTime</strong>" is derived from "Quality Time," symbolizing our hope to help people cherish every moment of their lives through our watches.</p>
        </section>

        <section class="content-section">
            <h2>Brand Story</h2>
            <p><?php echo $company_name; ?>'s founder, <strong><?php echo $founder; ?></strong>, is a Malaysian watchmaker who has been passionate about mechanics and craftsmanship since childhood. He began his career in a traditional watch shop in Kuala Lumpur, where he developed the idea of creating a watch brand that truly represents Malaysia. In <?php echo $year_founded; ?>, <?php echo $founder; ?> officially established <?php echo $company_name; ?>, aiming to bring Malaysian watchmaking craftsmanship to the global stage.</p>
            <p>Each <?php echo $company_name; ?> watch carries <?php echo $founder; ?>'s deep understanding of time and his unwavering pursuit of craftsmanship. We believe that time is more than just the passing of numbers; it is a witness to every precious moment in life.</p>
        </section>

        <section class="content-section">
            <h2>Product Features</h2>
            <ul>
                <li><strong>Made in Malaysia:</strong> Every <?php echo $company_name; ?> watch is meticulously crafted in Malaysia, combining the dedication of local artisans with an international design approach.</li>
                <li><strong>High-Quality Materials:</strong> We use premium materials such as stainless steel, ceramic, and sapphire crystal to ensure durability and elegance.</li>
                <li><strong>Diverse Designs:</strong> From classic business styles to sporty casual options, <?php echo $company_name; ?> offers a variety of designs to suit different occasions and individuals.</li>
                <li><strong>Water Resistance:</strong> Our watches feature excellent water resistance, making them suitable for everyday wear and even underwater activities.</li>
            </ul>
        </section>

        <section class="content-section">
            <h2>Design Philosophy</h2>
            <p><strong><?php echo $company_name; ?>'s design philosophy is "Simple, Yet Significant."</strong> We believe that true great design stands the test of time. Whether it's a classic three-hand layout or a sophisticated multi-functional dial, we strive for perfection in every detail. Each <?php echo $company_name; ?> watch merges modern aesthetics with practical functionality, making it ideal for daily wear and a treasured collectible.</p>
        </section>

        <section class="content-section">
            <h2>Customer Experience</h2>
            <ul>
                <li><strong>Lifetime Warranty:</strong> We offer a lifetime warranty on all <?php echo $company_name; ?> watches, ensuring long-term value for your investment.</li>
                <li><strong>Local Support:</strong> As a proudly Malaysian brand, we have dedicated customer service centers to provide timely assistance and support.</li>
                <li><strong>Exclusive Gift Box:</strong> Every <?php echo $company_name; ?> watch comes in an exquisite gift box, making it a perfect choice for gifting or personal use.</li>
            </ul>
        </section>

        <section class="content-section">
            <h2>Values & Commitment</h2>
            <ul>
                <li><strong>Uncompromising Quality:</strong> We guarantee that every watch undergoes strict quality control to ensure precision and durability.</li>
                <li><strong>Customer First:</strong> We always prioritize our customers' needs, offering thoughtful pre-sales and after-sales services.</li>
                <li><strong>Supporting Local Craftsmanship:</strong> Proudly made in Malaysia, we are dedicated to promoting local watchmaking craftsmanship and providing employment opportunities for local artisans.</li>
                <li><strong>Sustainability:</strong> We are committed to environmental sustainability by using renewable materials and eco-friendly packaging to minimize our impact on the planet.</li>
            </ul>
        </section>

        <section class="content-section">
            <h2>Vision</h2>
            <p><?php echo $company_name; ?> aspires to become a leading watch brand in Malaysia and globally, not only by excelling in craftsmanship and design but also by inspiring people with a deeper appreciation for time and life. Through <?php echo $company_name; ?> watches, we hope more people can experience the beauty and value of time while showcasing Malaysia's watchmaking expertise to the world.</p>
        </section>

        <section class="content-section contact-info">
            <h2>Contact Information (Malaysia Region)</h2>
            <p><strong>Address:</strong> <?php echo $hq_address; ?></p>
            <p><strong>Phone:</strong> <?php echo $phone; ?></p>
            <p><strong>Email:</strong> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
            <p><strong>Website:</strong> <a href="https://<?php echo $website; ?>" target="_blank"><?php echo $website; ?></a></p>
            <p><strong>Follow us:</strong> Instagram (<a href="https://www.instagram.com/<?php echo $instagram; ?>" target="_blank">@<?php echo $instagram; ?></a>), Facebook (<a href="https://www.facebook.com/<?php echo $facebook; ?>" target="_blank"><?php echo $facebook; ?></a>)</p>
        </section>
    </main>

   

    <script src="aboutus.js"></script>

    <?php include '../foot.php'; ?>
</body>
</html>