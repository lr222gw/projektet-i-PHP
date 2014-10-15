-- ================================================
-- Template generated from Template Explorer using:
-- Create Procedure (New Menu).SQL
--
-- Use the Specify Values for Template Parameters 
-- command (Ctrl-Shift-M) to fill in the parameter 
-- values below.
--
-- This block of comments will not be included in
-- the definition of the procedure.
-- ================================================
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE usp_addTranslatedStory
	@storyToTranslateID int,
	@thisMemberID int, 
	@thisLangTypeID int, 
	@thisStory text, 
	@title varchar(25), 
	@TranslateAuthor varchar(25) = ''
AS
BEGIN
	BEGIN TRY
	DECLARE @errormess varchar(55);
	set @errormess  = '';
		BEGIN TRAN
			set @errormess  = 'Error when inserted translatedstory into storyTranslationtabel';
			INSERT INTO storyTranslation(storyID ,memberID, langTypeID, translatedStory)
			VALUES(@storyToTranslateID, @thisMemberID, @thisLangTypeID, @thisStory);

			set @errormess  = 'Error with IF statement';
			IF (@TranslateAuthor = '')
			BEGIN 			
				set @TranslateAuthor = (select userName from member where memberID = @thisMemberID);				
			END
			ELSE
			BEGIN
				set @TranslateAuthor = @TranslateAuthor;
			END		

			set @errormess  = 'Error when inserted storyDetailType for Translator';
			INSERT INTO detailTypeOnStory(storyID, detailTypeID, detailValue)
			VALUES(@storyToTranslateID, 5, @TranslateAuthor);

			set @errormess  = 'Error when inserted storyDetailType for Title';			
			INSERT INTO detailTypeOnStory(storyID, detailTypeID, detailValue)
			VALUES(@storyToTranslateID, 3, @title);

		COMMIT TRAN
	END TRY
	BEGIN CATCH
		ROLLBACK TRAN
		Raiserror(@errormess, 16,1);
	END CATCH

END
