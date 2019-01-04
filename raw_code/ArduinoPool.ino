// Following includes are for Arduino Ethernet Shield (W5100)
// If you're using another shield, see Boards_* examples
#include <OneWire.h>
#include <DallasTemperature.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

const char* ssid     = "Canaa";      // SSID
const char* password = "nivealucasivan";      // Password
const char* host = "sa2.com.br";  // IP serveur - Server IP
const int   port = 80;            // Port serveur - Server Port
const int   watchdog = 10000;        // Fréquence du watchdog - Watchdog frequency
unsigned long previousMillis = millis();

#define ONE_WIRE_BUS 13

#define HEAT_PUMP_PIN 5
#define MAIN_PUMP_PIN 4
#define FULL_SYSTEM_RUNDOWN 60
#define DELTA_HIST 0.3

// Setup DS18B20
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);
DeviceAddress DS18B20[3];

#define TMETER0_CORRECTION 0
#define TMETER1_CORRECTION 1.4

long last_minute = 0;
long last_second = 0;
long last_hour = 0;
long last_half_hour = 0;
long last_ten_minutes = 0;
long max_millis_main_pump = 0;

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
  INIT_DS18B20(12);

  pinMode(HEAT_PUMP_PIN,OUTPUT);
  pinMode(MAIN_PUMP_PIN,OUTPUT);

  printTemps();
  sendTemps();
  runFullDiag();
  sendEvents("Boot - setup() performed");
}

void loop(void)
{
  onLoop();
}

void onMinute() {
  Serial.println("onMinute() ");

  printTemps();
  sendTemps();
  checkMainPumpPerm();

  last_minute = millis();
}

void onSecond() {
  //  printTemps();

  last_second = millis();
}

void onHour() {
  Serial.println("onHour() ");

  runDiagChooser();

  last_hour = millis();
}

void onHalfHour() {
  Serial.println("onHalfHour() ");

  runHeatBreaker();

  last_half_hour = millis();
}

void onTenMinutes() {
  Serial.println("onTenMinutes() ");

  last_ten_minutes = millis();
}

void onLoop() {
  long mils = millis();

  if(mils > (last_hour + 3600000)) onHour();
  if(mils > (last_half_hour + 1800000)) onHalfHour();
  if(mils > (last_ten_minutes + 600000)) onTenMinutes();
  if(mils > (last_minute + 60000)) onMinute();
  if(mils > (last_second + 1000)) onSecond();
}

bool tempDeltaDiag() {

  if(validTemps() && (getTemp(0) < (getTemp(1) - DELTA_HIST))) return true;
  else return false;
}

void printTemps() {

  Serial.print("Getting Temps...");
  sensors.requestTemperatures();
  Serial.println("Got");
  Serial.print("tempc0: ");
  Serial.println(getTemp(0));
  Serial.print("tempc1: ");
  Serial.println(getTemp(1));
  Serial.print("tempDeltaDiag(): ");
  Serial.println(tempDeltaDiag());
}

void runFullDiag() {
  int localDelay = FULL_SYSTEM_RUNDOWN * 1000;
  long rFD_start = millis();
  Serial.println("runFullDiag()");

  turnHeatPump(1);

  Serial.println("runFullDiag() - StartDelay");

  while(millis() < (rFD_start + localDelay)) {
    Serial.println("runFullDiag() - Delay");
    printTemps();
    yield();
  }

  if(validTemps() && tempDeltaDiag())  turnHeatPump(1);
  else   turnHeatPump(0);

  sendEvents("runFullDiag() performed");
}

void runDiagChooser() {
  int state = digitalRead(HEAT_PUMP_PIN);
  int net_perm = getPermissionDiag();

  Serial.println("runDiagChooser()");

  if(state && !tempDeltaDiag())  turnHeatPump(0);
  if(!state && net_perm != 0)   runFullDiag();
}

void turnHeatPump(bool state_hp) {
  digitalWrite(HEAT_PUMP_PIN,state_hp);

  String text = "turnHeatPump() performed, state ";
  text += String(state_hp);

  sendEvents(text);
}

void turnMainPump(bool state_hp) {
  digitalWrite(MAIN_PUMP_PIN,state_hp);
  String text = "turnMainPump() performed, state ";
  text += String(state_hp);

  sendEvents(text);
}

void checkMainPumpPerm() {
  if(getPermissionMainPump() == 1) {
    max_millis_main_pump = millis() + 300000;
  } else {
    max_millis_main_pump = 0;
  }

  if(millis() < max_millis_main_pump && !digitalRead(MAIN_PUMP_PIN)) turnMainPump(1);
  if(millis() > max_millis_main_pump && digitalRead(MAIN_PUMP_PIN)) turnMainPump(0);
}

void runHeatBreaker() {
  int state = digitalRead(HEAT_PUMP_PIN);

  if(validTemps() && state && !tempDeltaDiag()) turnHeatPump(0);

}

void sendTemps() {
  Serial.println("sendTemps()");

  unsigned long currentMillis = millis();

  if ( currentMillis - previousMillis > watchdog ) {
    previousMillis = currentMillis;
    WiFiClient client;

    if (!client.connect(host, port)) {
      Serial.println("connection failed");
      return;
    }

    String url = "/canaa/pool_be.php?method=temp_report&millis=";
    url += String(millis());
    url += "&tempc0=";
    url += getTemp(0);
    url += "&tempc1=";
    url += getTemp(1);
    url += "&heat_pump=";
    url += digitalRead(HEAT_PUMP_PIN);
    url += "&main_pump=";
    url += digitalRead(MAIN_PUMP_PIN);

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
      yield();
    }

    // Read all the lines of the reply from server and print them to Serial
    while(client.available()){
      String line = client.readStringUntil('\r');
      Serial.print(line);
      yield();
    }
  }
}

void sendEvents(String event_text) {
  Serial.println("sendEvents()");

  unsigned long currentMillis = millis();

  event_text = urlencode(event_text);

  if ( currentMillis - previousMillis > watchdog ) {
    previousMillis = currentMillis;
    WiFiClient client;

    if (!client.connect(host, port)) {
      Serial.println("connection failed");
      return;
    }

    String url = "/canaa/pool_be.php?method=events&millis=";
    url += String(millis());
    url += "&event_text=";
    url += event_text;

    // Envoi la requete au serveur - This will send the request to the server
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
    "Host: " + host + "\r\n" +
    "Connection: close\r\n\r\n");
    unsigned long timeout = millis();
    while (client.available() == 0) {
      if (millis() - timeout > 5000) {
        Serial.println(">>> Client Timeout !");
        client.stop();
        return;
      }
      yield();
    }

    // Read all the lines of the reply from server and print them to Serial
    while(client.available()){
      String line = client.readStringUntil('\r');
      Serial.print(line);
      yield();
    }
  }
}

bool validTemps() {
  float tempc1 = getTemp(0);
  float tempc2 = getTemp(1);
  bool tempc1_ok = false;
  bool tempc2_ok = false;

  if(tempc1 > 1 && tempc1 < 84) tempc1_ok = true;
  if(tempc2 > 1 && tempc2 < 84) tempc2_ok = true;

  if(tempc1_ok && tempc2_ok) return true;
  else return false;

}

float getTemp(int index) {
  float return_temp = -500;
  for (int i = 0; i < 100; i++){

    return_temp = sensors.getTempCByIndex(index);
    if (return_temp > 1 && return_temp < 84)  break;
    yield();
  }

  if(index == 0) return_temp = return_temp + TMETER0_CORRECTION;
  if(index == 1) return_temp = return_temp + TMETER1_CORRECTION;

  return return_temp;
}

void INIT_DS18B20(int precision)
{
  sensors.begin();

  int available = sensors.getDeviceCount();

  for(int x = 0; x!= available; x++)
  {
    if(sensors.getAddress(DS18B20[x], x))
    {
      sensors.setResolution(DS18B20[x], precision);
    }
    yield();
  }
}

String urlencode(String str)
{
  String encodedString="";
  char c;
  char code0;
  char code1;
  char code2;
  for (int i =0; i < str.length(); i++){
    c=str.charAt(i);
    if (c == ' '){
      encodedString+= '+';
    } else if (isalnum(c)){
      encodedString+=c;
    } else{
      code1=(c & 0xf)+'0';
      if ((c & 0xf) >9){
        code1=(c & 0xf) - 10 + 'A';
      }
      c=(c>>4)&0xf;
      code0=c+'0';
      if (c > 9){
        code0=c - 10 + 'A';
      }
      code2='\0';
      encodedString+='%';
      encodedString+=code0;
      encodedString+=code1;
      //encodedString+=code2;
    }
    yield();
  }
  return encodedString;
}

int getPermissionDiag() {
  HTTPClient http;
  http.setTimeout(10000);

  String url = "http://";
  url += host;
  url += "/canaa/pool_be.php?method=permission_diag";

  http.begin(url);

  int httpCode = http.GET();

  if(httpCode != 200) {
    http.end();
    Serial.println(httpCode);

    return -1;
  }
  else {
    String payload = http.getString();
    //Serial.println(payload);
    http.end();

    return payload.toInt();
  }

}

int getPermissionMainPump() {
  HTTPClient http;
  http.setTimeout(10000);

  String url = "http://";
  url += host;
  url += "/canaa/pool_be.php?method=main_pump";

  http.begin(url);

  int httpCode = http.GET();

  if(httpCode != 200) {
    http.end();
    Serial.println(httpCode);

    return -1;
  }
  else {
    String payload = http.getString();
    //Serial.println(payload);
    http.end();

    return payload.toInt();
  }
}
