DROP TABLE Users;
CREATE TABLE Users(
  UserID INTEGER PRIMARY KEY,
  Username VARCHAR(20),
  FirstName TEXT,
  LastName TEXT,
  Email TEXT,
  PasswordHash TEXT,
  Salt TEXT,
  EmailAlert INTEGER,
  BrowserAlert INTEGER,
  GroupID INTEGER,
  FOREIGN KEY(GroupID) REFERENCES Groups(GroupID)
);

DROP TABLE Groups;
CREATE TABLE Groups(
  GroupID INTEGER PRIMARY KEY,
  GroupName VARCHAR(20),
  OwnerID INTEGER,
  FOREIGN KEY(OwnerID) REFERENCES Users(UserID)
);

DROP TABLE Bills;
CREATE TABLE Bills(
  BillID INTEGER PRIMARY KEY,
  BillName VARCHAR(30),
  OwnerID INTEGER,
  Details TEXT,
  DueDate DATE,
  FOREIGN KEY(OwnerID) REFERENCES Users(UserID)
);

DROP TABLE Shares;
CREATE TABLE Shares(
  ShareID INTEGER PRIMARY KEY,
  BillID INTEGER,
  PayerID INTEGER,
  PaidAmount INTEGER,
  DueAmount INTEGER,
  Paid INTEGER,
  FOREIGN KEY(BillID) REFERENCES Bills(BillID),
  FOREIGN KEY(PayerID) REFERENCES Users(UserID)
);

DROP TABLE FeedAlerts;
CREATE TABLE FeedAlerts(
  AlertID INTEGER PRIMARY KEY,
  Title TEXT,
  Date DATE,
  Body TEXT,
  IsRead INTEGER,
  RecipientID INTEGER,
  FOREIGN KEY(RecipientID) REFERENCES Users(UserID)
);

DROP TABLE BrowserAlerts;
CREATE TABLE BrowserAlerts(
  AlertID INTEGER PRIMARY KEY,
  Message TEXT,
  RecipientID INTEGER,
  FOREIGN KEY(RecipientID) REFERENCES Users(UserID)
);
