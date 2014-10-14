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
CREATE PROCEDURE usp_regUser 
	-- Add the parameters for the stored procedure here
	@thisusername varchar(25),
	@userpassword varchar(100),
	@firstname varchar(25),
	@lastname varchar(25)
AS
BEGIN
-- lägg till användare, lösenord + användarnamn 
	BEGIN TRY
		BEGIN TRAN
			INSERT INTO member(userName, userPassword, firstName, lastName)
			VALUES (@thisusername, @userpassword, @firstname, @lastname);
		COMMIT TRAN
	END TRY
	BEGIN CATCH
		ROLLBACK TRAN
		RAISERROR('Problem when inserting member into database', 16,1);
	END CATCH

END
GO
