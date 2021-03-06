USE [199508-creepyreads]
GO
/****** Object:  StoredProcedure [dbo].[usp_addStory]    Script Date: 2014-10-14 15:31:23 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
ALTER PROCEDURE [dbo].[usp_addStory]   
	-- Add the parameters for the stored procedure here
	@thisMemberID int, 
	@thisLangTypeID int, 
	@thisStory text, 
	@title varchar(25), 
	@genre int, 
	@author varchar(25) = ''
AS
BEGIN
	BEGIN TRY
	DECLARE @errormess varchar(55);
	set @errormess  = '';
		BEGIN TRAN
					
			set @errormess  = 'Error when inserted story into storytabel';
			INSERT INTO story(memberID, langTypeID, story)
			VALUES(@thisMemberID, @thisLangTypeID, @thisStory);

			set @errormess  = 'Error with IF statement';
			IF (@author = '')
			BEGIN 			
				set @author = (select userName from member where memberID = @thisMemberID);				
			END
			ELSE
			BEGIN
				set @author = @author;
			END
			
			declare @StoryIDvar int;
			set @StoryIDvar = @@IDENTITY;

			set @errormess  = 'Error when inserted storyDetailType for Author';
			INSERT INTO detailTypeOnStory(storyID, detailTypeID, detailValue)
			VALUES(@StoryIDvar, 1, @author);

			set @errormess  = 'Error when inserted storyDetailType for Title';			
			INSERT INTO detailTypeOnStory(storyID, detailTypeID, detailValue)
			VALUES(@StoryIDvar, 3, @title);

			set @errormess  = 'Error when inserted genre';
			INSERT INTO genre(storyID, typeOfGenreID)
			VALUES(@StoryIDvar, @genre);


		COMMIT TRAN
	END TRY
	BEGIN CATCH
		ROLLBACK TRAN
		Raiserror(@errormess, 16,1);
	END CATCH

END
