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
CREATE PROCEDURE usp_changeStory   
	-- Add the parameters for the stored procedure here
	@thisStoryID int,
	@newStory text
AS
BEGIN
	BEGIN TRY
	DECLARE @errormess varchar(25);
	set @errormess  = '';
		BEGIN TRAN

			set @errormess  = 'Error when trying to update story';	
			UPDATE story
			SET story = @newStory
			WHERE storyID = @thisStoryID;

		COMMIT TRAN
	END TRY
	BEGIN CATCH
		ROLLBACK TRAN
		Raiserror(@errormess, 16,1);
	END CATCH

END
GO

