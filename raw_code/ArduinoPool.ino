// Following includes are for Arduino Ethernet Shield (W5100)
// If you're using another shield, see Boards_* examples
#include <OneWire.h>
#include <DallasTemperature.h>
#include <ESP8266WiFi.h>

const char* ssid     = "Canaa";      // SSID
const char* password = "nivealucasivan";      // Password
const char* host = "sa2.com.br";  // IP serveur - Server IP
const int   port = 80;            // Port serveur - Server Port
const int   watchdog = 5000;        // Fréquence du watchdog - Watchdog frequency
unsigned long previousMillis = millis(); 



// Fio referente aos dados vai conectado ao pino 2
#define ONE_WIRE_BUS 13

#define HEAT_PUMP_PIN 5
#define MAIN_PUMP_PIN 4
#define FULL_SYSTEM_RUNDOWN 60
#define DELTA_HIST 1


// Setup Inicial
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

long last_minute = 0;
long last_second = 0;
long last_hour = 0;


void setup(void)
{
  // start serial port
  Serial.begin(115200);

  // https://diyprojects.io/esp8266-web-client-tcp-ip-communication-examples-esp8266wifi-esp866httpclient/#.XCun91xKjIU
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");  
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  
  
  // Start up the library
  sensors.begin();

  pinMode(HEAT_PUMP_PIN,OUTPUT);
  pinMode(MAIN_PUMP_PIN,OUTPUT);

  printTemps();
  sendTemps();
  runFullDiag();
}

void loop(void)
{ 
  onLoop();
}

void onMinute() {
  Serial.println("onMinute() ");
  
  printTemps();
  sendTemps();
  runHeatBreaker();

  last_minute = millis();
}

void onSecond() {

  last_second = millis();
}

void onHour() {
  Serial.println("onHour() ");

  runDiagChooser();

  last_hour = millis();
}

void onLoop() {
   long mils = millis();

   if(mils > (last_hour + 360000)) onHour();
   if(mils > (last_minute + 60000)) onMinute();
   if(mils > (last_second + 1000)) onSecond();
}

bool tempDeltaDiag() {

  if(sensors.getTempCByIndex(0) < (sensors.getTempCByIndex(1) - DELTA_HIST)) return true;
  else return false;
}


void printTemps() {

  Serial.print("Getting Temps...");
  sensors.requestTemperatures();
  Serial.println("Got");
  Serial.print("tempc0: ");
  Serial.println(sensors.getTempCByIndex(0));  
  Serial.print("tempc1: ");
  Serial.println(sensors.getTempCByIndex(1));  
  Serial.print("tempDeltaDiag(): ");
  Serial.println(tempDeltaDiag());
}


void runFullDiag() {
   int localDelay = FULL_SYSTEM_RUNDOWN * 1000;

  Serial.println("runFullDiag()");
  
  digitalWrite(HEAT_PUMP_PIN,HIGH);

  Serial.println("runFullDiag() - StartDelay");
  delay(localDelay);

  if(tempDeltaDiag())   digitalWrite(HEAT_PUMP_PIN,HIGH);
  else   digitalWrite(HEAT_PUMP_PIN,LOW);
}

void runDiagChooser() {
  int state = digitalRead(HEAT_PUMP_PIN);

  Serial.println("runDiagChooser()");
  

  if(state && !tempDeltaDiag()) digitalWrite(HEAT_PUMP_PIN,LOW);
  if(!state)   runFullDiag();
}

void runHeatBreaker() {
  int state = digitalRead(HEAT_PUMP_PIN);

  if(state && !tempDeltaDiag()) digitalWrite(HEAT_PUMP_PIN,LOW);
 
}

void sendTemps() {
  unsigned long currentMillis = millis();

  if ( currentMillis - previousMillis > watchdog ) {
    previousMillis = currentMillis;
    WiFiClient client;
  
    if (!client.connect(host, port)) {
      Serial.println("connection failed");
      return;
    }

    String url = "/canaa/pool_be.php?millis=";
    url += String(millis());
    url += "&tempc0=";
    url += sensors.getTempCByIndex(0);
    url += "&tempc1=";
    url += sensors.getTempCByIndex(1);
    
    // Envoi la requete au serveur - This will send the request to the server
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" + 
               "Connection: close\r\n\r\n");
    unsigned long timeout = millis();
    while (client.available() == 0) {
      if (millis() - timeout > 10000) {
        Serial.println(">>> Client Timeout !");
        client.stop();
        return;
      }
    }
  
    // Read all the lines of the reply from server and print them to Serial
    while(client.available()){
      String line = client.readStringUntil('\r');
      Serial.print(line);
    }
  }
}
