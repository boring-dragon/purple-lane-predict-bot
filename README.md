# purple-lane-predict-bot

Predict 


### How this works & Steps

- [x] Get all the Live fixtures from worldcup API
- [x] Get the fixtures to predict from PurpleLane API
- [x] Convert Local time ( Qatar Time) to Maldives Time
- [x] Mins Before `prediction_closes_at` equals to current time. Get the live score of the world cup fixture
- [ ] Predict with for the given email with away team score & home team score
- [ ] Host & run the app periodically on server


#### Some Routes

### Get Fixtures

Email could be your mail

Get Request: `https://foariapp.com/api/fixtures?tournament_uuid=97c1c316-c945-4f28-a4bc-d087e6e35f2d&email=j@live.mv`


### Update Prediction


Get Request: `https://foariapp.com/api/prediction?email=yoosufyazak%40gmail.com&fixture_id=29&away_team_score=2&home_team_score=2`

Sample response:

```json
{
	"status": true,
	"prediction": {
		"id": 2092,
		"uuid": "97d518bd-46f2-4b66-b64c-f5b9a7bb7ed4",
		"participant_id": "279",
		"fixture_id": 29,
		"home_team_score": "2",
		"away_team_score": "2",
		"status": 0,
		"prize_won": 0,
		"won": 0,
		"created_at": "2022-11-25T22:23:08.000000Z",
		"updated_at": "2022-11-25T22:29:23.000000Z"
	}
}
```
