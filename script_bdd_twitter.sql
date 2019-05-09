CREATE DATABASE twitter_ter;

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
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS region
(
    id         SERIAL PRIMARY KEY,
    region     VARCHAR(100) NOT NULL,
    id_country INT          NOT NULL,
    FOREIGN KEY (id_country) REFERENCES country (id)
);

CREATE TABLE IF NOT EXISTS city
(
    id        SERIAL PRIMARY KEY,
    city      VARCHAR(100) NOT NULL,
    id_region INT          NOT NULL,
    FOREIGN KEY (id_region) REFERENCES region (id)
);

CREATE TABLE IF NOT EXISTS media
(
    id   SERIAL PRIMARY KEY,
    type VARCHAR(100) NULL,
    url  VARCHAR(500) NOT NULL
);

CREATE TABLE IF NOT EXISTS language
(
    id   SERIAL PRIMARY KEY,
    lang VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS keyword
(
    id         SERIAL PRIMARY KEY,
    keyword    VARCHAR(100) NOT NULL,
    id_subject INT          NULL,
    FOREIGN KEY (id_subject) REFERENCES subject (id)
);

CREATE TABLE IF NOT EXISTS hashtag
(
    id         SERIAL PRIMARY KEY,
    hashtag    VARCHAR(100) NOT NULL,
    id_subject INT          NULL,
    FOREIGN KEY (id_subject) REFERENCES subject (id)
);

CREATE TABLE IF NOT EXISTS subject
(
    id      SERIAL PRIMARY KEY,
    subject VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS tweeter_user
(
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    friends_count   INT          NOT NULL DEFAULT 0,
    screen_name     VARCHAR(100) NOT NULL,
    followers_count INT          NOT NULL DEFAULT 0,
    location        VARCHAR(100) NULL,
    uid             BIGINT       NULL
);

CREATE TABLE IF NOT EXISTS follow
(
    id              SERIAL PRIMARY KEY,
    id_tweeter_user INT NOT NULL,
    FOREIGN KEY (id_tweeter_user) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweeter_user_follow
(
    id_tweeter_user  INT NOT NULL,
    id_followed_user INT NOT NULL,
    FOREIGN KEY (id_tweeter_user) REFERENCES tweeter_user (id),
    FOREIGN KEY (id_followed_user) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweet
(
    id              SERIAL PRIMARY KEY,
    oid             VARCHAR(255) NOT NULL UNIQUE,
    id_city         INT          NOT NULL,
    id_tweet_user   INT          NULL,
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
    id_tweet INT NOT NULL,
    id_media INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_media) REFERENCES media (id)
);

CREATE TABLE IF NOT EXISTS tweet_keyword
(
    id_tweet   INT NOT NULL,
    id_keyword INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_keyword) REFERENCES keyword (id)
);

CREATE TABLE IF NOT EXISTS tweet_hashtag
(
    id_tweet   INT NOT NULL,
    id_hashtag INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_hashtag) REFERENCES hashtag (id)
);

CREATE TABLE IF NOT EXISTS tweet_mention
(
    id_tweet   INT NOT NULL,
    id_mention INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_mention) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweet_like
(
    id_tweet     INT NOT NULL,
    id_user_like INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_user_like) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweet_reply
(
    id_tweet      INT NOT NULL,
    id_user_reply INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_user_reply) REFERENCES tweeter_user (id)
);

CREATE TABLE IF NOT EXISTS tweet_retweet
(
    id_tweet        INT NOT NULL,
    id_user_retweet INT NOT NULL,
    FOREIGN KEY (id_tweet) REFERENCES tweet (id),
    FOREIGN KEY (id_user_retweet) REFERENCES tweeter_user (id)
);
