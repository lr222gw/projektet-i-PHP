


select story.storyID, scoreValue.scoreValue
from story
	left join score	on score.storyID = story.storyID
	left JOIN scoreValue on score.scoreValueID = scoreValue.scoreValueID
where story.storyID = 90;