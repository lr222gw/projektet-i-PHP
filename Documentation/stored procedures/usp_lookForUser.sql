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
CREATE PROCEDURE usp_lookForUser 
	-- Add the parameters for the stored procedure here
	@thisusername varchar(25)
AS
BEGIN
-- leta reda p� anv�ndare

SELECT userName
FROM member
WHERE userName = @thisusername;

-- s� anv�ndarnamn ej kan reggas mer �n 1 g�ng. (returnerar anv�ndarnamnet om det finns...)

END
GO
