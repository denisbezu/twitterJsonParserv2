# from french_lefff_lemmatizer.french_lefff_lemmatizer import FrenchLefffLemmatizer
import psycopg2
from stop_words import get_stop_words
import re
import spacy
from spacy_lefff import LefffLemmatizer, POSTagger

nlp = spacy.load('en')
lematizer = LefffLemmatizer()
nlp.add_pipe(lematizer, name='lefff')
stopWordsFr = get_stop_words('fr')
stopWordsEn = get_stop_words('en')

conn = psycopg2.connect(host="localhost", database="twitter_ter", user="postgres", password="123")

def getLemas(str, lang):
    outStr = ""
    array = str.split()
    for word in array:
        outStr += re.sub(r'[\W]', '', word) + " "
    if lang == 'fr':
        textToProcess = list(filter(lambda x: x not in stopWordsFr, outStr.split()))
    else:
        textToProcess = list(filter(lambda x: x not in stopWordsEn, outStr.split()))
    textToProcess = " ".join(textToProcess)
    doc = nlp(textToProcess)

    lemas = list()
    for d in doc:
        lemas.append(d.lemma_)

    return lemas


queryLema = "SELECT id FROM word_bag as wb WHERE wb.word_bag = %s"

insertLema = "INSERT INTO word_bag(word_bag) VALUES (%s) RETURNING id;"

insertLemaTweet = "INSERT INTO tweet_word_bag(id_tweet, id_word_bag) VALUES (%s, %s)"

def insertLemas(lemas, idTweet):
    for lema in lemas:
        lemaCursor: psycopg2._psycopg.cursor = conn.cursor()
        insertCursor: psycopg2._psycopg.cursor = conn.cursor()
        insertLemaTweetCursor: psycopg2._psycopg.cursor = conn.cursor()
        try:
            lemaCursor.execute(queryLema, (lema,))
            if lemaCursor.rowcount == 0:
                insertCursor.execute(insertLema, (lema,))
                id = insertCursor.fetchone()[0]
                conn.commit()
            else:
                id = lemaCursor.fetchone()[0]
            insertLemaTweetCursor.execute(insertLemaTweet, (idTweet, id,))
            conn.commit()

        except (Exception, psycopg2.DatabaseError) as error:
            print(error)
            conn.rollback()
        finally:
            lemaCursor.close()
            insertLemaTweetCursor.close()
            insertCursor.close()


cursor: psycopg2._psycopg.cursor = conn.cursor()
# queryFr = "SELECT t.id, t.tweet_text, l.lang FROM tweet as t" \
#           " INNER JOIN language l on t.id_language = l.id " \
#           "WHERE lang = 'fr'"

queryEn = "SELECT t.id, t.tweet_text, l.lang FROM tweet as t" \
          " INNER JOIN language l on t.id_language = l.id " \
          "WHERE lang = 'en'"

cursor.execute(queryEn)
counter = 0

while True:
    row = cursor.fetchone()
    if row is None:
        break
    print(str(counter) + '\n')
    lemas = getLemas(row[1], row[2])
    insertLemas(lemas, row[0])
    counter += 1

conn.close()
