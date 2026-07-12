
CREATE SCHEMA IF NOT EXISTS rental_fraud_db DEFAULT CHARACTER SET utf8mb4;
USE rental_fraud_db;

-- 1. 사용자 테이블
CREATE TABLE IF NOT EXISTS users (
  user_id VARCHAR(50) NOT NULL,
  name VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  birth_date DATE NOT NULL,
  social_login_type VARCHAR(20) DEFAULT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  role ENUM('user', 'admin') DEFAULT 'user',
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. 게시글
CREATE TABLE IF NOT EXISTS post (
  post_id INT AUTO_INCREMENT PRIMARY KEY,
  author_id VARCHAR(50),
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  region VARCHAR(50),
  fraud_type VARCHAR(50),
  contract_stage ENUM('before', 'during', 'after') DEFAULT NULL,
  views INT DEFAULT 0,
  FOREIGN KEY (author_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. 댓글
CREATE TABLE IF NOT EXISTS comment (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  author_id VARCHAR(50),
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. 공지사항
CREATE TABLE IF NOT EXISTS notice (
  notice_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  admin_id VARCHAR(50),
  FOREIGN KEY (admin_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. 신고
CREATE TABLE IF NOT EXISTS report (
  report_id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  reporter_id VARCHAR(50),
  reason TEXT NOT NULL,
  reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE,
  FOREIGN KEY (reporter_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. 스크랩
CREATE TABLE IF NOT EXISTS scrap (
  post_id INT NOT NULL,
  user_id VARCHAR(50) NOT NULL,
  scraped_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. 체크리스트
CREATE TABLE IF NOT EXISTS checklist (
  checklist_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(50),
  item TEXT NOT NULL,
  is_checked TINYINT(1) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. 보험 추천 입력
CREATE TABLE IF NOT EXISTS insurance_recommendation_input (
  recommendation_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(50),
  recommended_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_homeless TINYINT(1) DEFAULT 0,
  deposit INT NOT NULL,
  target_type VARCHAR(20) NOT NULL,
  income_level VARCHAR(50) NOT NULL,
  housing_type VARCHAR(50) NOT NULL,
  region VARCHAR(50) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. 보험 추천 결과
CREATE TABLE IF NOT EXISTS insurance_recommendation_result (
  recommendation_id INT PRIMARY KEY,
  insurance_company VARCHAR(50),
  guarantee_limit INT,
  guarantee_fee INT,
  is_eilgible TINYINT(1) DEFAULT 0,
  required_document TEXT,
  FOREIGN KEY (recommendation_id) REFERENCES insurance_recommendation_input(recommendation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
