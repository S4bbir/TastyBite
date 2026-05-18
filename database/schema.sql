CREATE DATABASE IF NOT EXISTS online_food_blog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE online_food_blog;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'member') NOT NULL DEFAULT 'member',
  profile_picture VARCHAR(255) NULL,
  remember_token VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  location VARCHAR(120) NOT NULL,
  area VARCHAR(120) NOT NULL,
  short_background TEXT NOT NULL,
  goals TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_menu_restaurant
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  menu_item_id INT NOT NULL,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_menu
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS food_experience_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  content TEXT NOT NULL,
  post_type ENUM('restaurant', 'food', 'both') NOT NULL DEFAULT 'food',
  restaurant_id INT NULL,
  menu_item_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_posts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_posts_restaurant
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
    ON DELETE SET NULL,
  CONSTRAINT fk_posts_menu
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS food_experience_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_post
    FOREIGN KEY (post_id) REFERENCES food_experience_posts(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_comments_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS restaurant_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_restaurant_reviews_restaurant
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_restaurant_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT chk_restaurant_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

INSERT IGNORE INTO users (id, name, email, password_hash, role) VALUES
(1, 'Site Admin', 'admin@foodblog.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin'),
(2, 'Member One', 'member@foodblog.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'member'),
(3, 'Member Two', 'writer@foodblog.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'member');

INSERT IGNORE INTO restaurants (id, name, location, area, short_background, goals) VALUES
(1, 'Spice Courtyard', 'Dhaka', 'Banani', 'A modern Bangladeshi restaurant known for homestyle curries, rice bowls, and family platters.', 'Celebrate regional ingredients with reliable service and affordable lunch choices.'),
(2, 'Green Fork Bistro', 'Dhaka', 'Dhanmondi', 'A casual cafe focused on fresh salads, grilled chicken, wraps, and juice blends.', 'Make healthy dining easy for students, office workers, and families.'),
(3, 'Noodle Harbor', 'Chattogram', 'GEC Circle', 'A compact Asian noodle bar serving quick bowls, dumplings, and spicy soups.', 'Bring fast, flavorful meals to busy city diners.'),
(4, 'Sweet Grain Bakery', 'Sylhet', 'Zindabazar', 'A neighborhood bakery with cakes, pastries, sourdough, and coffee.', 'Offer fresh daily baking and a calm place for dessert lovers.');

INSERT IGNORE INTO menu_items (id, restaurant_id, name, description, price, image_path) VALUES
(1, 1, 'Bhuna Beef Bowl', 'Slow-cooked beef bhuna served with steamed rice, salad, and house pickle.', 320.00, NULL),
(2, 1, 'Chicken Tehari', 'Fragrant rice cooked with spiced chicken, potatoes, and caramelized onions.', 250.00, NULL),
(3, 2, 'Lemon Herb Chicken Salad', 'Grilled chicken over greens with cucumber, olives, citrus dressing, and seeds.', 280.00, NULL),
(4, 2, 'Paneer Wrap', 'Soft flatbread filled with paneer, peppers, onion, lettuce, and mint yogurt.', 220.00, NULL),
(5, 3, 'Fire Chili Noodles', 'Stir-fried noodles with chili oil, vegetables, egg, and your choice of protein.', 260.00, NULL),
(6, 3, 'Chicken Dumplings', 'Steamed dumplings with sesame soy dip and fresh scallions.', 180.00, NULL),
(7, 4, 'Chocolate Ganache Cake', 'Rich chocolate layer cake with ganache frosting and cocoa crumb.', 190.00, NULL),
(8, 4, 'Butter Croissant', 'Flaky croissant baked fresh each morning with cultured butter.', 140.00, NULL);

INSERT IGNORE INTO reviews (id, menu_item_id, user_id, comment) VALUES
(1, 1, 2, 'The beef was tender and the portion was generous. Great lunch option.'),
(2, 5, 3, 'Very spicy in the best way. The noodles stayed chewy and fresh.'),
(3, 7, 2, 'Dense, chocolatey, and not too sweet.');

INSERT IGNORE INTO restaurant_reviews (id, restaurant_id, user_id, rating, comment) VALUES
(1, 1, 2, 5, 'Friendly staff and a comfortable place for family dinner.'),
(2, 2, 3, 4, 'Fresh ingredients and quick service.');

INSERT IGNORE INTO food_experience_posts (id, user_id, title, content, post_type, restaurant_id, menu_item_id) VALUES
(1, 2, 'A rainy lunch at Spice Courtyard', 'The Bhuna Beef Bowl was exactly the kind of warm, comforting meal that works on a rainy afternoon. The pickle balanced the richness nicely.', 'both', 1, 1),
(2, 3, 'Why I keep returning for Fire Chili Noodles', 'Noodle Harbor gets the heat level right. The chili oil has flavor, not just spice, and the bowl travels well for takeaway.', 'food', 3, 5);

INSERT IGNORE INTO food_experience_comments (id, post_id, user_id, comment) VALUES
(1, 1, 3, 'That bowl is my favorite too. The pickle makes it.'),
(2, 2, 2, 'I need to try the dumplings next time.');

