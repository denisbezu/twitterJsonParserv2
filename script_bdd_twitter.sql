-- CREATE DATABASE twitter_ter;

CREATE TABLE IF NOT EXISTS year
(
    id   SERIAL PRIMARY KEY,
    year INT NOT NULL
);

CREATE TABLE IF NOT EXISTS month
(
    id      SERIAL PRIMARY KEY,
    month   INT NOT NULL,
    id_year INT NOT NULL,
    FOREIGN KEY (id_year) REFERENCES year (id)
);

CREATE TABLE IF NOT EXISTS day
(
    id       SERIAL PRIMARY KEY,
    day      INT NOT NULL,
    id_month INT NOT NULL,
    FOREIGN KEY (id_month) REFERENCES month (id)
);


CREATE TABLE IF NOT EXISTS country
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS region
(
    id         SERIAL PRIMARY KEY,
    region     VARCHAR(100) NOT NULL UNIQUE,
    id_country INT          NOT NULL,
    FOREIGN KEY (id_country) REFERENCES country (id)
);

CREATE TABLE IF NOT EXISTS city
(
    id        SERIAL PRIMARY KEY,
    city      VARCHAR(100) NOT NULL UNIQUE,
    id_region INT          NOT NULL,
    FOREIGN KEY (id_region) REFERENCES region (id)
);

CREATE TABLE IF NOT EXISTS media
(
    id   SERIAL PRIMARY KEY,
    type VARCHAR(100) NULL,
    url  VARCHAR(500) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS language
(
    id   SERIAL PRIMARY KEY,
    lang VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS subject
(
    id      SERIAL PRIMARY KEY,
    subject VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS hashtag
(
    id         SERIAL PRIMARY KEY,
    hashtag    VARCHAR(100) NOT NULL,
    id_subject INT          NULL,
    FOREIGN KEY (id_subject) REFERENCES subject (id)
);

CREATE TABLE IF NOT EXISTS keyword
(
    id         SERIAL PRIMARY KEY,
    keyword    VARCHAR(100) NOT NULL,
    id_subject INT          NULL,
    FOREIGN KEY (id_subject) REFERENCES subject (id)
);


CREATE TABLE IF NOT EXISTS tweeter_user
(
    id              VARCHAR(100) PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    friends_count   INT          NULL,
    screen_name     VARCHAR(100) NOT NULL,
    followers_count INT          NULL,
    location        VARCHAR(100) NULL
);

CREATE TABLE IF NOT EXISTS follow
(
    id              INT PRIMARY KEY,
    id_tweeter_user VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_tweeter_user) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweeter_user_follow
(
    id_tweeter_user  VARCHAR(100) NOT NULL,
    id_followed_user VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_tweeter_user) REFERENCES tweeter_user (id),
    FOREIGN KEY (id_followed_user) REFERENCES tweeter_user (id),
    PRIMARY KEY (id_tweeter_user, id_followed_user)
);

CREATE TABLE IF NOT EXISTS tweet
(
    id              VARCHAR(255) PRIMARY KEY,
    id_city         INT          NULL,
    id_tweet_user   VARCHAR(100) NULL,
    id_day          INT          NOT NULL,
    id_language     INT          NOT NULL,
    source          VARCHAR(100) NOT NULL,
    link            VARCHAR(100) NOT NULL,
    retweet_count   INT          NOT NULL,
    favourite_count INT          NOT NULL,
    tweet_text      VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_city) REFERENCES city (id),
    FOREIGN KEY (id_tweet_user) REFERENCES tweeter_user (id),
    FOREIGN KEY (id_day) REFERENCES day (id),
    FOREIGN KEY (id_language) REFERENCES language (id)
);

CREATE TABLE IF NOT EXISTS tweet_media
(
    id_tweet VARCHAR(255) NOT NULL,
    id_media INT          NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_media) REFERENCES media (id),
    PRIMARY KEY (id_tweet, id_media)
);

CREATE TABLE IF NOT EXISTS tweet_keyword
(
    id_tweet   VARCHAR(255) NOT NULL,
    id_keyword INT          NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_keyword) REFERENCES keyword (id),
    PRIMARY KEY (id_tweet, id_keyword)
);

CREATE TABLE IF NOT EXISTS tweet_hashtag
(
    id_tweet   VARCHAR(255) NOT NULL,
    id_hashtag INT          NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_hashtag) REFERENCES hashtag (id),
    PRIMARY KEY (id_tweet, id_hashtag)
);

CREATE TABLE IF NOT EXISTS tweet_mention
(
    id_tweet   VARCHAR(255) NOT NULL,
    id_mention VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_mention) REFERENCES tweeter_user (id),
    PRIMARY KEY (id_tweet, id_mention)
);

CREATE TABLE IF NOT EXISTS tweet_like
(
    id_tweet     VARCHAR(255) NOT NULL,
    id_user_like VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_user_like) REFERENCES tweeter_user (id),
    PRIMARY KEY (id_tweet, id_user_like)

);

CREATE TABLE IF NOT EXISTS tweet_reply
(
    id_tweet      VARCHAR(255) NOT NULL,
    id_user_reply VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_tweet, id_user_reply)
);

CREATE TABLE IF NOT EXISTS tweet_retweet
(
    id_tweet        VARCHAR(255) NOT NULL,
    id_user_retweet VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_tweet, id_user_retweet)
);
