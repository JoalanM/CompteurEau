//Importation bibliothèque
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiClient.h>
#include <Arduino.h>
#include <Ticker.h>
//*******************************

Ticker send_consomation;

//Définition PIN
#define PIN_RELAIS 5
#define PIN_CAPTEUR_EAU 4
//**************************

int etat_precedent_relais = digitalRead(PIN_RELAIS);

//compteur d'eau
//etat précédent compteur
int etat_precedent = LOW;
//Compteur litre 
int litre = 0;
int consommation_precedent = 0;
int consommation_actuelle =0;
//delais sans rebond
#define delai_sans_rebond 2000000 //2secondes
//**********************************************


//Info WiFi
const char *ssid = "snir"; // Enter your WiFi name
const char *password = "12345678";  // Enter WiFi password
//**********************************************************

//Info MQTT Broker
const char *mqtt_broker = "192.168.5.74";
const char *topic = "topic/relais";
const char *mqtt_username = "esp8266";
const char *mqtt_password = "esp8266";
const int mqtt_port = 1883;
//*****************************************

//Client MQTT
WiFiClient espClient;
PubSubClient client(espClient);
//*******************************

//Fonction d'interruption & compteur litres
void IRAM_ATTR get_litre()
{
    unsigned long _micros;
    int etat_actuelle = digitalRead(PIN_CAPTEUR_EAU);
    if(etat_actuelle!=etat_precedent)
    {
        _micros =micros();
        //Serial.print("Etat actuelle : ");
        //Serial.println(digitalRead(PIN_RELAIS));

        if(_micros>=delai_sans_rebond)
        {
          if(digitalRead(PIN_CAPTEUR_EAU)==HIGH)
          {
            litre++;
            Serial.print(litre);
            Serial.println(" litre");

          }
        }
        
        etat_precedent = etat_actuelle;
        _micros = 0;
    } 

}
//************************************************************


//Setup 

void setup() {
    // serial baud à 115200;
    Serial.begin(115200);
    Serial.println(" ");

    // Connexion au réseau WiFi 
    Serial.print("Connexion");
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) 
    {
        delay(2000);
        Serial.print(".");
    }
    Serial.println(" ");
    Serial.println("Connexion au réseau Wi-Fi établie : ");

    //Information Connexion
    Serial.println();
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
    Serial.print("Passerelle IP : ");
    Serial.println(WiFi.gatewayIP());
    Serial.print("DNS IP : ");
    Serial.println(WiFi.dnsIP());
    Serial.print("Puissance de réception : ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");
    //***********************************************************


    //Connexion au serveur mqtt broker
    client.setServer(mqtt_broker, mqtt_port);
    client.setCallback(callback);
    while (!client.connected()) {
        String client_id = "esp8266 client mqtt";
        Serial.println("Connexion au Boker Mqtt");
        if (client.connect(client_id.c_str(), mqtt_username, mqtt_password)) {
            Serial.println("Connexion établie !");
        } else {
            Serial.println("Echec de connexion :  ");
            Serial.println(client.state());
            delay(2000);
        }
    }
    //********************************************************************************

    // publish and subscribe
    client.publish(topic, "hello esp8266");
    client.subscribe(topic);
    //********************************************************************************

    //Définir etat relai en fonction du dernier etat enregistrer 
    WiFiClient clienthttp;
    HTTPClient http;
    

    //Reqête GET
    if(http.begin(clienthttp, "http://192.168.5.74/php-api/index.php?ID=2"))
    {
        int httpCode = http.GET();

        if(httpCode > 0)
        {
            Serial.printf("[HTTP] GET... code: %d\n", httpCode);

        }
        if(httpCode == HTTP_CODE_OK || httpCode ==  HTTP_CODE_MOVED_PERMANENTLY)
        {
            
            String payload = http.getString();
            //Serial.println(payload);


            char json[500];
            payload.replace(" ", "");
            payload.replace("\n", "");
            payload.trim();
            payload.remove(0, 1);
            payload.toCharArray(json, 500);

            DynamicJsonDocument doc(1024);
            deserializeJson(doc, json);

            const char* etat = doc["etat"];
            Serial.println(String(etat));

            if(String(etat)=="ON")
            {
                digitalWrite(PIN_RELAIS, LOW);
                Serial.println("Courrant circule ");
            }
            else if (String(etat) == "OFF")
            {
                digitalWrite(PIN_RELAIS, HIGH);
                Serial.println("Courrant fermée ");
            }
            
        }
        else
        {
            Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());

        }
        http.end();
    }
    else
    {
        Serial.printf("[HTTP} Unable to connect\n");
    }
    //digitalWrite(PIN_RELAIS, HIGH);
    
    

    pinMode(PIN_RELAIS, OUTPUT);
    pinMode(PIN_CAPTEUR_EAU, INPUT_PULLUP);
    attachInterrupt(PIN_CAPTEUR_EAU, get_litre, CHANGE); //Fonction d'interruption 
    send_consomation.attach(30,set_litre); //30 x 60 = 1800 s

}

void callback(char *topic, byte *payload, unsigned int length) {
  //Serial.print("Message arrivée sur le topic: ");
  //Serial.println(topic);
  //Serial.print("Message:");
  String message;
  for (int i = 0; i < length; i++) 
    {
        Serial.print((char) payload[i]);
        message += (char)payload[i];
    }
    Serial.println(" ");
    if (message == "1")
    {
        digitalWrite(PIN_RELAIS,LOW);
    }
    else if(message == "0")
    {
        digitalWrite(PIN_RELAIS,HIGH);
    } 
}

void loop() 
{
    if(!client.connected())
    {
        reconnect();
    }
    client.loop();
    int etat_actuelle_relais = digitalRead(PIN_RELAIS);
    if(etat_actuelle_relais!=etat_precedent_relais)
    {
        //Serial.print("Etat actuelle : ");
        //Serial.println(digitalRead(PIN_RELAIS));
        if(digitalRead(PIN_RELAIS)==LOW)
        {
           client.publish("topic/relais", "Etat actuelle : Ouvert");
        }
        else
        {
            client.publish("topic/relais", "Etat actuelle : Fermer");
        }
        etat_precedent_relais = etat_actuelle_relais;
    }

    //********************************************************************************



}

void reconnect()
{
    //Connexion au serveur mqtt broker
    client.setServer(mqtt_broker, mqtt_port);
    client.setCallback(callback);
    while (!client.connected()) {
        String client_id = "esp8266 client mqtt";
        Serial.println("Connexion au Boker Mqtt");
        if (client.connect(client_id.c_str(), mqtt_username, mqtt_password)) {
            Serial.println("Connexion établie !");
        } else {
            Serial.println("Echec de connexion :  ");
            Serial.println(client.state());
            delay(2000);
        }
    }
    //********************************************************************************

    // publish and subscribe
    client.publish(topic, "hello esp8266");
    client.subscribe(topic);
    //********************************************************************************

}



void set_litre()
{
    if(!client.connected())
    {
        reconnect();
    }
    else
    {
        consommation_actuelle = litre-consommation_precedent;
        consommation_precedent+=consommation_actuelle;
        char const *litre_char;
        String mystr; 
        mystr=String(consommation_actuelle);
        litre_char = mystr.c_str();
        client.publish("topic/consommation", litre_char); 
    }
    

}
