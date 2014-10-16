SELECT story.storyID, story, userName, (select detailValue from detailTypeOnStory where detailTypeID = 3 AND story.storyID = detailTypeOnStory.storyID) as title, genreTypeValue as genre, langType.typeOfLangType, (select detailValue from detailTypeOnStory where detailTypeID = 1 AND story.storyID = detailTypeOnStory.storyID) as Author
FROM story	
	left JOIN genre On  genre.storyID = story.storyID
	left Join typeOfGenre ON typeOfGenre.typeOfGenreID = genre.typeOfGenreID
	left join member ON story.memberID = member.memberID
	left join langType on story.langTypeID = langType.langTypeID	
;