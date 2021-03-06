--Add each trigger one by one.

--Trigger for Locations--
DELIMITER $$
 CREATE TRIGGER `Before_Insert_Location` BEFORE INSERT ON `Visited`
 FOR EACH ROW BEGIN
  IF (EXISTS(SELECT 1 FROM Visited d WHERE d.Username = NEW.Username && d.Location_ID = New.Location_ID)) THEN
    SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'INSERT failed due to duplicate mobile number';
    END IF;
END;
$$
DELIMITER ;

--Trigger for Dog_Profile--
DELIMITER $$
 CREATE TRIGGER `Before_Insert_Dog` BEFORE INSERT ON `Dog_Profile`
 FOR EACH ROW BEGIN
  IF (EXISTS(SELECT 1 FROM Dog_Profile d WHERE d.Username = NEW.Username)) THEN
SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'INSERT failed due to duplicate mobile number';
END IF;
END;
$$
DELIMITER ;
