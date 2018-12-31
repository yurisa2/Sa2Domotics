// Following includes are for Arduino Ethernet Shield (W5100)
// If you're using another shield, see Boards_* examples
#include <OneWire.h>
#include <DallasTemperature.h>

// Fio referente aos dados vai conectado ao pino 2
#define ONE_WIRE_BUS 2

#define HEAT_PUMP_PIN 2
#define FULL_SYSTEM_RUNDOWN 180
#define DELTA_HIST 0.5


// Setup Inicial
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

long last_minute = 0;
long last_second = 0;
long last_hour = 0;


void setup(void)
{
  // start serial port
  Serial.begin(9600);
  // Start up the library
  sensors.begin();

  pinMode(HEAT_PUMP_PIN,OUTPUT);
}

void loop(void)
{ 

  Serial.print("Esperando a temperatura...");
  sensors.requestTemperatures();
  Serial.println("Pronto");
  Serial.print("Temperatura para o dispositivo 1 (index 0) é: ");
  Serial.println(sensors.getTempCByIndex(0));  
  Serial.print("Temperatura para o dispositivo 2 (index 1) é: ");
  Serial.println(sensors.getTempCByIndex(1));  

  onLoop();
}

void onMinute() {

  last_minute = millis();
}

void onSecond() {

  last_second = millis();
}

void onHour() {

  runDiagChooser();

  last_hour = millis();
}

void onLoop() {
   long mils = millis();

   if(mils > (last_hour + 3600000)) onHour();
   if(mils > (last_minute + 600000)) onMinute();
   if(mils > (last_second + 1000)) onSecond();
}

bool tempDeltaDiag() {

  if(sensors.getTempCByIndex(0) < (sensors.getTempCByIndex(1) - DELTA_HIST)) return true;
  else return false;
}

void runFullDiag() {
   int localDelay = FULL_SYSTEM_RUNDOWN * 1000;
  
  digitalWrite(HEAT_PUMP_PIN,HIGH);
  delay(localDelay);

  if(tempDeltaDiag())   digitalWrite(HEAT_PUMP_PIN,HIGH);
  else   digitalWrite(HEAT_PUMP_PIN,LOW);
}

void runDiagChooser() {
  int state = digitalRead(HEAT_PUMP_PIN);

  if(state && !tempDeltaDiag()) digitalWrite(HEAT_PUMP_PIN,LOW);
  if(!state)   runFullDiag();
}
