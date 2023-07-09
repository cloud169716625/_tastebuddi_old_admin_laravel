#!/bin/bash
curl -X POST -H "Authorization: key=AAAAzJYdgIk:APA91bF0rhxQok78rJH4QzOz8WLt71Uw1gScUwyTfdnxV9teCVZbXUdECm-jdjnJYcekOYiU9f1LKGvS321hA3sfrwDIFHWpVqY-O8VNjrPSyJ4ZQEZEwIyD0sKfuLyU7-c28CpPl54o" -H "Content-Type: application/json" \
   -d '{
  "data": {
    "notification": {
        "icon": "notification.png",
        "title": "Abby",
        "body": "Normal"
    }
  },
  "to": "f0V5aAjGW7I:APA91bGlshinKXIAWR97SotBcihmmZ5ZxhZeZ5z-IXIZX1E64eVl3KOmKa8dMnGdHuwpXtfsvMtW5l03JZUmWWcoB2PfOZB1OZ58LqoMZa83Ih9B20i5Flu9wpAAP9mcBAwHCsnZwXvl"
}' https://fcm.googleapis.com/fcm/send
