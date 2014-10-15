CREATE PROCEDURE usp_getAllStoriesFromUserBackpack
	-- Add the parameters for the stored procedure here
	@memberID int
AS
BEGIN

	SELECT storyID, translateID
	from storyInBackpack
	where memberID = @memberID;

END
GO