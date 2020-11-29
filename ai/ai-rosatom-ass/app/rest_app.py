import os
import traceback
import pickle
import string
import pymorphy2
import flask

from flask import Flask, request, jsonify, json
from werkzeug.exceptions import HTTPException
from sklearn.feature_extraction.text import TfidfVectorizer

from nltk.corpus import stopwords


def create_app():
    app = Flask(__name__)
    app_dir = os.path.dirname(__file__)
    
    model_path = os.path.join(app_dir, 'model.pickle')
    vect_path = os.path.join(app_dir, 'vect.pickle')
    
    app.model = pickle.load(open(model_path, "rb"))
    app.vect = pickle.load(open(vect_path, "rb"))
    
    return app
    
app = create_app()

def get_unigramms(text):

    charsforexcluding = string.punctuation + '«»№•–’‘”“\n\t¬…—'
    morph = pymorphy2.MorphAnalyzer()
    stop_words = [x for x in stopwords.words('english') + stopwords.words('russian') if not x in ["не"]]
    stop_words += ["коллега","просить","просьба","здравствовать","спасибо","пожалуйста","уважаемый", "уважение"]
    stop_words = set(stop_words)

    if isinstance(text, str):
        text = " " + text.lower() + " "
        text = text.replace("добрый день", "").replace("доброе утро", "")

        unigramms = ""
        # заменим пунктуацию на пробел
        for x in charsforexcluding:
            text = text.replace(x, ' ')

        for el in text.split():

            el = el.lower()
            el = el.replace(" ", "")

            if (not el.isdigit()) & (not el in stop_words):
                if el != '':

                    prs = morph.parse(el)[0]
                    nf = prs.normal_form

                    if nf not in stop_words:
                        if unigramms == " ":
                            unigramms = nf
                        else:
                            unigramms += " "
                            unigramms += nf

        return unigramms

    else:
        return ""   

   
@app.errorhandler(Exception)
def handle_error(e):
    """Global exception handler for all unhandled exceptions"""
    code = 500
    if isinstance(e, HTTPException):
        code = e.code
    return jsonify(success=False,error=str(e), stacktrace=traceback.format_exc(),), code

def response(code, data):
    resp = flask.jsonify(data)
    resp.status_code = code
    return resp

@app.route("/predict", methods=['POST'])
def predict():
    data = request.json
    text = data['text']
    unigramms = get_unigramms(text)
    unigramms_vect = app.vect.transform([unigramms])
    
    probas = app.model.predict_proba(unigramms_vect)
    class_indexes = (-probas).argsort(axis=1)  # индексы
    class_output = app.model.classes_[class_indexes]

    MAX_RESULTS = 3
    output_data = []
    for class_idx, class_name in zip(class_indexes[0][:MAX_RESULTS], class_output[0][:MAX_RESULTS]):
        output_data.append({"value": int(class_name), "prob": probas[0][class_idx]})
    
    return response(200, output_data)
    # result = int(app.model.predict(unigramms_vect)[0])
    # return response(200, {'class': result})
    

    
if __name__ == '__main__':
    app.run(host='0.0.0.0')