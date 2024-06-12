import json
import sys
import pandas as pd
from sklearn.preprocessing import StandardScaler
from sklearn.metrics.pairwise import cosine_similarity
import pymysql.cursors


json_data = [
    {"title":"Toyota Hilux","Availability":" Diesel ","Year":"2018","Engine":"2","Transmission":"Automatic","Price":" 2018 "},
    {"title":"Toyota Harrier ","Availability":"In Stock","Year":"2017","Engine":"2000","Transmission":"Auto","Price":"3.75M"},
    {"title":"Land Rover RangeRover Vogue","Availability":" Petrol ","Year":"2015","Engine":"3","Transmission":"Automatic","Price":" 2015 "},
    {"title":"Land Rover Discovery IV","Availability":" Diesel ","Year":"2016","Engine":"3","Transmission":"Automatic","Price":" 2016 "},
    {"title":"Toyota Wish ","Availability":"In Stock","Year":"2014","Engine":"1800","Transmission":"Auto","Price":"1.4M"},
    {"title":"Mazda CX-5 ","Availability":"In Stock","Year":"2017","Engine":"2000","Transmission":"Auto","Price":"2.99M"},
    {"title":"Mercedes-Benz C200 ","Availability":"In Stock","Year":"2016","Engine":"2000","Transmission":"Auto","Price":"4.6M"},
    {"title":"Toyota Hilux","Availability":" Diesel ","Year":"2014","Engine":"3","Transmission":"Manual","Price":" 2014 "},
    {"title":"Audi A3 ","Availability":"In Stock","Year":"2016","Engine":"1400","Transmission":"Auto","Price":"2.1M"},
    {"title":"Toyota Alphard ","Availability":"In Stock","Year":"2017","Engine":"2500","Transmission":"Auto","Price":"4.2M"},
    {"title":"Toyota Prado New Shape TX","Availability":"In Stock","Year":"2020","Engine":"2700","Transmission":"Auto","Price":"9.5M"},
    {"title":"Toyota Land Cruiser","Availability":" Petrol ","Year":"2013","Engine":"4","Transmission":"Automatic","Price":" 2013 "},
    {"title":"Audi A5 ","Availability":"In Stock","Year":"2017","Engine":"2000","Transmission":"Auto","Price":"5.15M"},
    {"title":"Toyota Fielder Hybrid ","Availability":"In Stock","Year":"2018","Engine":"1500","Transmission":"Auto","Price":"1.98M"},
    {"title":"Toyota Land Cruiser Prado TZ.G ","Availability":"In Stock","Year":"2022","Engine":"2800","Transmission":"Auto","Price":"10.8M"},
    {"title":"Toyota Ch-R-Hybrid ","Availability":"In Stock","Year":"2016","Engine":"1800","Transmission":"Auto","Price":"3.3M"},
    {"title":"Suzuki Swift ","Availability":"In Stock","Year":"2016","Engine":"1500","Transmission":"Auto","Price":"1.07M"},
    {"title":"Toyota Probox ","Availability":"In Stock","Year":"2018","Engine":"1500","Transmission":"Auto","Price":"1.38M"},
    {"title":"Lexus RX200T ","Availability":"In Stock","Year":"2016","Engine":"2000","Transmission":"Auto","Price":"5.85M"},
    {"title":"BMW X1","Availability":" Diesel ","Year":"2017","Engine":"2","Transmission":"Automatic","Price":" 2017 "},
    {"title":"Lexus LX570","Availability":" Petrol ","Year":"2016","Engine":"5","Transmission":"Automatic","Price":" 2016 "},
    {"title":"Mercedes Benz GLA 250","Availability":" Petrol ","Year":"2015","Engine":"2","Transmission":"Automatic","Price":" 2015 "},
    {"title":"Alpha Romeo Sportiva ","Availability":"In Stock","Year":"2013","Engine":"1400","Transmission":"Auto","Price":"1.25M"},
    {"title":"Toyota FJ Cruiser ","Availability":"In Stock","Year":"2011","Engine":"4000","Transmission":"Auto","Price":"2.75M"},
    {"title":"Toyota Land Cruiser Prado","Availability":" Diesel ","Year":"2017","Engine":"3","Transmission":"Automatic","Price":" 2017 "},
    {"title":"Toyota Land Cruiser Prado","Availability":" Petrol ","Year":"2010","Engine":"2","Transmission":"Automatic","Price":" 2010 "},
    {"title":"Mercedes Benz E200","Availability":" Petrol ","Year":"2017","Engine":"2","Transmission":"Automatic","Price":" 2017 "},
    {"title":"Nissan note","Availability":"Petrol","Year":"2017","Engine":"1200","Transmission":"Automatic","Price":"1,180,000"},
    {"title":"Toyota Land Cruiser Prado","Availability":" Diesel ","Year":"2017","Engine":"2","Transmission":"Automatic","Price":" 2017 "},
    {"title":"Porsche Cayenne","Availability":" Diesel ","Year":"2014","Engine":"3","Transmission":"Automatic","Price":" 2014 "},
    {"title":"Toyota Land Cruiser Prado TX","Availability":"In Stock","Year":"2016","Engine":"2800","Transmission":"Auto","Price":"4.99M"},
    {"title":"Toyota Landcruiser 300 Series ZX","Availability":"In Stock","Year":"2022","Engine":"3400","Transmission":"Auto","Price":"23.5M"},
    {"title":"Porsche Cayenne","Availability":" Diesel ","Year":"2016","Engine":"3","Transmission":"Automatic","Price":" 2016 "},
    {"title":"Toyota Sienta","Availability":"Petrol","Year":"2016","Engine":"1500","Transmission":"Automatic","Price":"1,280,000"},
    {"title":"Toyota Succeed","Availability":"Petrol","Year":"2017","Engine":"1490","Transmission":"Automatic","Price":"1,350,000"},
    {"title":"Toyota Axio","Availability":"Petrol","Year":"2018","Engine":"1490","Transmission":"Automatic","Price":"1,750,000"},
    {"title":"Suzuki Landy","Availability":"Petrol","Year":"2015","Engine":"1999","Transmission":"Automatic","Price":"1,480,000"},
    {"title":"DEAL ! 2017 MERCEDES C180 AMG","Availability":"Petrol","Year":"2017","Engine":"2","Transmission":"Automatic","Price":"3,299,999"},
    {"title":"Nissan Xtrail","Availability":"Petrol","Year":"2017","Engine":"2000","Transmission":"Automatic","Price":"2,950,000"},
    {"title":"Mazda demio","Availability":"Diesel","Year":"2014","Engine":"1490","Transmission":"Automatic","Price":"880,000"},
    {"title":"Volks Wagen Touran","Availability":"Petrol","Year":"2014","Engine":"1400","Transmission":"Automatic","Price":"1,450,000"},
    {"title":"Mercedes-Benz A180 ","Availability":"In Stock","Year":"2016","Engine":"1600","Transmission":"Auto","Price":"2.3M"},
    {"title":"BRAND NEW 2018 TOYOTA HARRIER GR SPORT","Availability":"Petrol","Year":"2018","Engine":"1980","Transmission":"Automatic","Price":"4,777,777"},
    {"title":"TOYOTA RAV4","Availability":"Petrol","Year":"2019","Engine":"2000","Transmission":"Automatic","Price":"3,155,000"},
    {"title":"2007 NISSAN NOTE E11 FOR SALE","Availability":"Petrol","Year":"2007","Engine":"1500","Transmission":"Automatic","Price":"390,000"},
    {"title":"Nissan B15","Availability":"Petrol","Year":"2001","Engine":"1490","Transmission":"Automatic","Price":"420,000"},
    {"title":"Nissan Juke","Availability":"Petrol","Year":"2012","Engine":"1500","Transmission":"Automatic","Price":"970,000"},
    {"title":"Toyota Crown ","Availability":"In Stock","Year":"2014","Engine":"2500","Transmission":"Auto","Price":"3.25M"},
    {"title":"Mercedes S350","Availability":"Petrol","Year":"2008","Engine":"3500","Transmission":"Automatic","Price":"1,950,000"},
    {"title":"Lexus RX 450h. YOM: 2011","Availability":"Petrol","Year":"2011","Engine":"3500","Transmission":"Automatic","Price":"2,200,000"}
]


df = pd.DataFrame(json_data)


df = df.drop(columns=['Transmission', 'Availability'])

# Convert Price to numerical format
df['Price'] = df['Price'].str.replace(',', '').str.extract(r'(\d+\.?\d*)').astype(float)

# Remove spaces and convert titles to lowercase
df['title'] = df['title'].apply(lambda x: x.strip().lower())

# Normalize numeric columns (excluding 'title')
scaler = StandardScaler()
df[df.columns.difference(['title'])] = scaler.fit_transform(df[df.columns.difference(['title'])])

# Calculate the cosine similarity matrix
cosine_sim = cosine_similarity(df.drop(columns=['title']), df.drop(columns=['title']))

# Function to get similar products
def get_similar_products(product_title, top_n=6):
    idx = df.index[df['title'] == product_title.strip().lower()].tolist()
    if idx:
        idx = idx[0]
        sim_scores = list(enumerate(cosine_sim[idx]))
        sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
        sim_scores = sim_scores[1:top_n+1]
        similar_indices = [i[0] for i in sim_scores]
        return df.iloc[similar_indices]['title'].tolist()
    else:
        return None


if len(sys.argv) < 3:
    print("Usage: python script.py <product_title> <user_id>")
    sys.exit(1)

product_title = sys.argv[1]
user_id = sys.argv[2]
similar_products = get_similar_products(product_title)

if not similar_products:
    print(f"No similar products found for '{product_title}'")
    sys.exit(0)

# Connect to the database
connection = pymysql.connect(host='localhost',
                             user='root',
                             password='',
                             db='e-commerce',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor)

try:
    with connection.cursor() as cursor:
        
        sql_delete_all = "DELETE FROM recommendationss"
        cursor.execute(sql_delete_all)

        
        sql_insert_recommendationss = "INSERT INTO recommendationss (title, car_id) VALUES (%s, %s)"
        for product in similar_products:
            cursor.execute(sql_insert_recommendationss, (product, 1))

    
        sql_insert_recommendations = "INSERT INTO recommendations (title, user_id) VALUES (%s, %s)"
        for product in similar_products[:2]:
            cursor.execute(sql_insert_recommendations, (product, user_id))

    
    connection.commit()
finally:
   
    connection.close()
