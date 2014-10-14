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
-- =============================================-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE usp_addStoryToBackpack   
	-- Add the parameters for the stored procedure here
	@memberID int, 
	@storyID int, 
	@translateID int = null
AS
BEGIN
	BEGIN TRY
	DECLARE @errormess varchar(25);
	set @errormess  = '';
		BEGIN TRAN

			
			IF (@translateID = null)	
			BEGIN
				set @errormess  = 'Error when inserting story into storyinbackpack';
				INSERT INTO storyInBackpack(storyID, memberID)
				values(@storyID, @memberID);
			END
			else 	
			BEGIN
				set @errormess  = 'Error when inserting Translatedstory into storyinbackpack';
				INSERT INTO storyInBackpack(storyID, memberID, translateID)
				values(@storyID, @memberID, @translateID);
			END

		COMMIT TRAN
	END TRY
	BEGIN CATCH
		ROLLBACK TRAN
		Raiserror(@errormess, 16,1);
	END CATCH

END
GO

