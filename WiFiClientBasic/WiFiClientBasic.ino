//Importation bibliothéque
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <ArduinoJson.h>
#include <Arduino.h>
#include <Ticker.h>
// *****************************


Ticker send_consomation;

//Definition PIN 
#define PIN_RELAIS 5
#define PIN_CAPTEUR_EAU 4
// ***************************



int etat_precedent_relais = digitalRead(PIN_RELAIS);

//Variable Filtrage Anti-Rebonds
bool didStatus  = false;
bool oldDidStatus  = false;
unsigned long lastDebounceTime  = 0;
unsigned long debounceDelay  = 50;
// ************************************

//Compteur litre 
int litre = 0;
int consommation_precedent = 0;
int consommation_actuelle =0;
// *****************************



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
// ******************************************

//Definition client mqtt
WiFiClient espClient;
PubSubClient client(espClient);
//******************************


//Fonction d'interruption
void IRAM_ATTR get_litre()
{
    int reading = digitalRead(PIN_CAPTEUR_EAU);
    if (reading != oldDidStatus) 
    {
        lastDebounceTime = millis();
    }
    if ((millis() - lastDebounceTime)>= debounceDelay) 
    {
        if (reading != didStatus) 
        {
            didStatus = reading;
        
            if(didStatus == 1)
            {
                litre++;
                Serial.print(litre); Serial.println("litres");
            }
            //Serial.print(F("Sensor state : ")); Serial.println(didStatus);
        }
  }
  oldDidStatus = reading;

}


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
                digitalWrite(PIN_RELAIS, HIGH);
                Serial.println("Courrant circule ");
            }
            else if (String(etat) == "OFF")
            {
                digitalWrite(PIN_RELAIS, LOW);
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
        digitalWrite(PIN_RELAIS,HIGH);
    }
    else if(message == "0")
    {
        digitalWrite(PIN_RELAIS,LOW);
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
        if(digitalRead(PIN_RELAIS)==HIGH)
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
    consommation_actuelle = litre-consommation_precedent;
    consommation_precedent+=consommation_actuelle;
    char const *litre_char;
    String mystr; 
    mystr=String(consommation_actuelle);
    litre_char = mystr.c_str();
    client.publish("topic/consommation", litre_char);

}