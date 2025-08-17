CREATE SCHEMA IF NOT EXISTS lbaw2476;

SET search_path TO lbaw2476;

DROP TABLE IF EXISTS LikedAuction;
DROP TABLE IF EXISTS Watchlist;
DROP TABLE IF EXISTS Messages;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS Notification;
DROP TABLE IF EXISTS Bid;
DROP TABLE IF EXISTS Report;
DROP TABLE IF EXISTS Auction;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS UnblockRequest;
DROP TABLE IF EXISTS Premium;
DROP TABLE IF EXISTS AuthenticatedUser;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS SubCategory;
DROP TABLE IF EXISTS Category;
DROP TABLE IF EXISTS ResetToken;
DROP TYPE IF EXISTS NotificationType;
DROP TYPE IF EXISTS ProductState;
DROP TYPE IF EXISTS AuctionState;
DROP FUNCTION IF EXISTS notify_users_auction_ended;
DROP FUNCTION IF EXISTS auction_search_update;
DROP FUNCTION IF EXISTS check_bid_exists;
DROP FUNCTION IF EXISTS check_bid_covered;
DROP FUNCTION IF EXISTS validate_new_bid;
DROP FUNCTION IF EXISTS notify_users_auction_start;
DROP FUNCTION IF EXISTS remove_auction_after_bid_function;
DROP FUNCTION IF EXISTS remove_watchlist_auction_ended;
DROP FUNCTION IF EXISTS delete_product_on_auction_delete;
DROP FUNCTION IF EXISTS handle_likedauction_delete;
DROP FUNCTION IF EXISTS create_message_auction_ended;
DROP FUNCTION IF EXISTS premium_sub_ended;
DROP FUNCTION IF EXISTS send_notification_message_received;
DROP FUNCTION IF EXISTS send_notification_new_review;
DROP FUNCTION IF EXISTS update_winner_balance;

CREATE TYPE NotificationType AS ENUM ('Auction End','Auction Start','Bid Covered','Review','Message','Auction Closed');

CREATE TYPE ProductState AS ENUM ('Brand New','Like New','Very Good','Good','Acceptable');

CREATE TYPE AuctionState AS ENUM ('Created','Started','Ended');

-- Create Category table
CREATE TABLE Category (
    catId SERIAL PRIMARY KEY,
    categoryName VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-solid fa-question'
);

-- Create SubCategory table
CREATE TABLE SubCategory (
    subCatId SERIAL PRIMARY KEY,
    subcategoryName VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-solid fa-question',
    catId INT NOT NULL,
    FOREIGN KEY (catId) REFERENCES Category(catId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create User table
CREATE TABLE Users (
    userId SERIAL PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(320) NOT NULL UNIQUE,
    password VARCHAR(256) NOT NULL,
    phoneNumber VARCHAR(15) NOT NULL UNIQUE CONSTRAINT phoneNumberLength CHECK (LENGTH(phoneNumber) <= 15),
    rememberToken VARCHAR(256)
);

-- Create Admin table
CREATE TABLE Admin (
    adminId SERIAL PRIMARY KEY,
    adminLevel INT NOT NULL,
    uId INT NOT NULL,
    FOREIGN KEY (uId) REFERENCES Users(userId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create AuthenticatedUser table
CREATE TABLE AuthenticatedUser (
    authId SERIAL PRIMARY KEY,
    address TEXT NOT NULL,
    registerDate DATE NOT NULL,
    uId INT NOT NULL,
    balance NUMERIC(10,2) NOT NULL DEFAULT 0,
    isBlocked BOOLEAN NOT NULL DEFAULT FALSE,
    profilePic TEXT NOT NULL DEFAULT 'https://picsum.photos/200/300',
    FOREIGN KEY (uId) REFERENCES Users(userId)
);

-- Create Premium table
CREATE TABLE Premium (
    premId SERIAL PRIMARY KEY,
    expiryDate DATE NOT NULL,
    authId INT NOT NULL,
    FOREIGN KEY (authId) REFERENCES AuthenticatedUser(authId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Product table
CREATE TABLE Product (
    productId SERIAL PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    description VARCHAR(500) NOT NULL,
    state ProductState NOT NULL,
    authId INT NOT NULL,
    subCatId INT NOT NULL,
    FOREIGN KEY (authId) REFERENCES AuthenticatedUser(authId),
    FOREIGN KEY (subCatId) REFERENCES SubCategory(subCatId) ON UPDATE CASCADE
);

-- Create Auction table
CREATE TABLE Auction (
    auctionId SERIAL PRIMARY KEY,
    initDate TIMESTAMP NOT NULL,
    closeDate TIMESTAMP NOT NULL,
    initValue NUMERIC(10, 2) NOT NULL,
    state AuctionState NOT NULL DEFAULT 'Created',
    productId INT NOT NULL,
    FOREIGN KEY (productId) REFERENCES Product(productId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Bid table
CREATE TABLE Bid (
    bidId SERIAL PRIMARY KEY,
    amount NUMERIC(10, 2) NOT NULL CONSTRAINT amountNegative CHECK (amount > 0),
    bidDate TIMESTAMP NOT NULL,
    authId INT NOT NULL,
    auctionId INT NOT NULL,
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (authId) REFERENCES AuthenticatedUser(authId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Notification table
CREATE TABLE Notification (
    notId SERIAL PRIMARY KEY,
    content VARCHAR(100) NOT NULL,
    type NotificationType NOT NULL,
    sentDate TIMESTAMP NOT NULL,
    seen BOOLEAN NOT NULL,
    authId INT NOT NULL,
    auctionId INT,
    FOREIGN KEY (authId) REFERENCES AuthenticatedUser(authId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Image table
CREATE TABLE Image (
    imageId SERIAL PRIMARY KEY,
    image TEXT NOT NULL,
    productId INT NOT NULL,
    FOREIGN KEY (productId) REFERENCES Product(productId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Review table
CREATE TABLE Review (
    reviewId SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    rating NUMERIC(3,2) CONSTRAINT ratingError CHECK (rating >= 1.00 AND rating <= 5.00),
    reviewDate TIMESTAMP NOT NULL,
    authIdReviewer INT NOT NULL,
    auctionId INT NOT NULL,
    FOREIGN KEY (authIdReviewer) REFERENCES AuthenticatedUser(authId),
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE
);

-- Create Messages table
CREATE TABLE Messages (
    messageId SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    sentDate TIMESTAMP NOT NULL,
    senderId INT NOT NULL,
    auctionId INT NOT NULL,
    FOREIGN KEY (senderId) REFERENCES AuthenticatedUser(authId),
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Watchlist table
CREATE TABLE Watchlist (
    watchId SERIAL PRIMARY KEY,
    authId INT NOT NULL,
    FOREIGN KEY (authId) REFERENCES AuthenticatedUser(authId) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create LikedAuction table
CREATE TABLE LikedAuction (
    likeId SERIAL PRIMARY KEY,
    watchId INT NOT NULL,
    auctionId INT NOT NULL,
    FOREIGN KEY (watchId) REFERENCES Watchlist(watchId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE UnblockRequest(
    unblockId SERIAL PRIMARY KEY,
    userId INT NOT NULL,
    content VARCHAR(150) NOT NULL,
    date TIMESTAMP NOT NULL,
    FOREIGN KEY (userId) REFERENCES Users(userId) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Report(
    reportId SERIAL PRIMARY KEY,
    userWhoReported INT NOT NULL,
    userReported INT NOT NULL,
    content VARCHAR(150) NOT NULL,
    date TIMESTAMP NOT NULL,
    auctionId INT NOT NULL,
    FOREIGN KEY (userWhoReported) REFERENCES Users(userId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (userReported) REFERENCES Users(userId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (auctionId) REFERENCES Auction(auctionId) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ResetToken(
    resetId SERIAL PRIMARY KEY,
    token VARCHAR(256) NOT NULL,
    email VARCHAR(320) NOT NULL,
    created_at TIMESTAMP NOT NULL
);



------------------------------------- Indexes --------------------------------------------------------------


CREATE INDEX bid_idauction_idx ON Bid USING HASH (auctionId);

CREATE INDEX auction_dates_idx ON Auction (initDate, closeDate);

CREATE INDEX notification_authid_idx ON Notification USING HASH (authId);


---------------------------------- Triggers -------------------------------------------------------------------

CREATE OR REPLACE FUNCTION notify_users_auction_ended() 
RETURNS TRIGGER AS $$
DECLARE
    auction_owner INT;
    interested_user RECORD;
    auction_winner INT;
BEGIN
    -- Notify the owner only if the auction is starting now or in the past
    IF NEW.closeDate <= NOW() THEN
        -- Get the owner of the auction
        SELECT p.authId 
        INTO auction_owner
        FROM Product p
        JOIN Auction a ON p.productId = a.productId
        WHERE a.auctionId = NEW.auctionId;

        -- Get the auction winner
        SELECT b.authId 
        INTO auction_winner
        FROM Bid b
        WHERE b.auctionId = NEW.auctionId
        ORDER BY b.amount DESC
        LIMIT 1;
        
        -- Notify the owner
        INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
        VALUES ('Your auction has ended.', 'Auction End', NOW(), FALSE, auction_owner, NEW.auctionId);

        -- Notify users in the watchlist for this auction
        FOR interested_user IN 
            SELECT w.authId 
            FROM Watchlist w 
            JOIN LikedAuction la ON w.watchId = la.watchId 
            WHERE la.auctionId = NEW.auctionId
        LOOP
            IF auction_winner IS NULL OR interested_user.authId != auction_winner THEN
                INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
                VALUES ('An auction you are interested in has ended.', 'Auction End', NOW(), FALSE, interested_user.authId, NEW.auctionId);
            END IF;
        END LOOP;

        -- If there's a winner, send a notification
        IF auction_winner IS NOT NULL THEN
            INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
            VALUES ('Congratulations! You won the auction.', 'Auction End', NOW(), FALSE, auction_winner, NEW.auctionId);
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER auction_ended_notification
AFTER UPDATE OF closeDate ON Auction
FOR EACH ROW
EXECUTE FUNCTION notify_users_auction_ended();


------------------------------------------------------------------------------------------------------------


ALTER TABLE Product
ADD COLUMN tsvectors TSVECTOR;


CREATE FUNCTION auction_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.title), 'A') ||
         setweight(to_tsvector('english', NEW.description), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.title <> OLD.title OR NEW.description <> OLD.description) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.title), 'A') ||
             setweight(to_tsvector('english', NEW.description), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$ LANGUAGE plpgsql;

CREATE TRIGGER auction_search_update_trigger
 BEFORE INSERT OR UPDATE ON Product
 FOR EACH ROW
 EXECUTE PROCEDURE auction_search_update();


CREATE INDEX auction_search_idx ON Product USING GIN (tsvectors);

------------------------------------------------------------------------------------------------------------


CREATE FUNCTION check_bid_exists() 
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 
        FROM product 
        JOIN auction ON auction.productid = product.productid
        WHERE auction.auctionid = NEW.auctionid 
          AND product.authid = NEW.senderid
    ) THEN
        IF NOT EXISTS (
            SELECT 1 
            FROM bid 
            WHERE authid = NEW.senderid 
              AND auctionid = NEW.auctionid
        ) THEN
            RAISE EXCEPTION 'User must have made a bid in the auction to send a message';
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER check_bid_exists_trigger
BEFORE INSERT ON Messages
FOR EACH ROW
EXECUTE FUNCTION check_bid_exists();


------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION update_winner_balance()
RETURNS TRIGGER AS $$
DECLARE 
    winner_id INT;
    final_bid_amount DECIMAL(10, 2);
BEGIN
    -- Get the highest bidder and the final bid amount
    SELECT b.authId, b.amount
    INTO winner_id, final_bid_amount
    FROM Bid b
    WHERE b.auctionId = NEW.auctionId
    ORDER BY b.amount DESC
    LIMIT 1;

    -- If there is a winner, update their balance
    IF winner_id IS NOT NULL THEN
        UPDATE AuthenticatedUser
        SET balance = balance - final_bid_amount
        WHERE authid = winner_id;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER update_winner_balance_trigger
AFTER UPDATE OF closeDate ON Auction
FOR EACH ROW
WHEN (NEW.closedate <= NOW())
EXECUTE FUNCTION update_winner_balance();


------------------------------------------------------------------------------------------------------------


CREATE FUNCTION check_bid_covered() RETURNS TRIGGER AS $$
DECLARE
    previous_top_bid_authId INT;
BEGIN
    -- Retrieve the second highest bid's authId for the auction, if it exists
    SELECT authId
    INTO previous_top_bid_authId
    FROM (
        SELECT authId, amount
        FROM Bid
        WHERE auctionId = NEW.auctionId
        ORDER BY amount DESC
        LIMIT 2
    ) AS top_bids
    ORDER BY amount ASC
    LIMIT 1;

    -- If there's a previous top bid and it belongs to a different user, notify them
    IF previous_top_bid_authId IS NOT NULL AND previous_top_bid_authId IS DISTINCT FROM NEW.authId THEN
        INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
        VALUES ('Your bid has been covered', 'Bid Covered', NOW(), FALSE, previous_top_bid_authId, NEW.auctionId);
    END IF;

    RETURN NEW;
END; $$ LANGUAGE plpgsql;


CREATE TRIGGER check_bid_covered_trigger
AFTER INSERT ON Bid
FOR EACH ROW
EXECUTE FUNCTION check_bid_covered();


------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION validate_new_bid() RETURNS TRIGGER AS $$
DECLARE
    current_highest_bid NUMERIC(10, 2);
    current_highest_bidder INT;
    step NUMERIC(10,2);
    init_value NUMERIC(10,2);

BEGIN
    SELECT b.amount, b.authId, a.initValue * 0.05
    INTO current_highest_bid, current_highest_bidder
    FROM Bid b
    JOIN Auction a ON b.auctionId = a.auctionId
    WHERE b.auctionId = NEW.auctionId
    ORDER BY b.amount DESC
    LIMIT 1;

    SELECT a.initvalue
    INTO init_value
    FROM Auction a
    WHERE a.auctionId = NEW.auctionId;

    IF (current_highest_bid IS NULL AND NEW.amount < init_value) THEN
        RAISE EXCEPTION 'New bid must be higher than the initial value.';
    END IF;

    IF (current_highest_bidder IS NOT NULL AND NEW.authId = current_highest_bidder) THEN
        RAISE EXCEPTION 'The highest bid is already from this user.';
    END IF;
    
    IF (current_highest_bid IS NOT NULL AND NEW.amount < current_highest_bid + step) THEN
        RAISE EXCEPTION 'New bid must be higher than the current highest bid.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER validate_new_bid_trigger
BEFORE INSERT ON Bid
FOR EACH ROW
EXECUTE FUNCTION validate_new_bid();

------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION notify_users_auction_start() 
RETURNS TRIGGER AS $$
DECLARE
    auction_owner INT;
    interested_user RECORD;
BEGIN
    -- Get the owner of the auction
    SELECT p.authId 
    INTO auction_owner
    FROM Product p
    JOIN Auction a ON p.productId = a.productId
    WHERE a.auctionId = NEW.auctionId;

    -- Notify the owner only if the auction is starting now or in the past
    IF NEW.initDate <= NOW() THEN
        -- Notify the owner
        INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
        VALUES ('Your auction has started.', 'Auction Start', NOW(), FALSE, auction_owner, NEW.auctionId);

        -- Notify users in the watchlist for this auction
        FOR interested_user IN 
            SELECT w.authId 
            FROM Watchlist w 
            JOIN LikedAuction la ON w.watchId = la.watchId 
            WHERE la.auctionId = NEW.auctionId
        LOOP
            INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
            VALUES ('An auction you are interested in has started.', 'Auction Start', NOW(), FALSE, interested_user.authId, NEW.auctionId);
        END LOOP;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER auction_start_notification
AFTER UPDATE OF initDate ON Auction
FOR EACH ROW
EXECUTE FUNCTION notify_users_auction_start();

------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION remove_auction_after_bid_function() 
RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM LikedAuction la WHERE likeId IN (SELECT likeID
                                                  FROM LikedAuction la1
                                                  JOIN Watchlist w ON w.watchId = la1.watchId
                                                  WHERE la1.auctionId = NEW.auctionId AND NEW.authId = w.authId);

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER remove_auction_after_bid
AFTER INSERT ON Bid
FOR EACH ROW
EXECUTE FUNCTION remove_auction_after_bid_function();

------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION remove_watchlist_auction_ended()
RETURNS TRIGGER AS $$
BEGIN

    IF NEW.closeDate <= NOW() THEN DELETE FROM LikedAuction WHERE new.auctionId = auctionId;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER remove_watchlist_auction_ended
AFTER UPDATE OF closeDate ON Auction
FOR EACH ROW
WHEN(NEW.closeDate <= NOW())
EXECUTE FUNCTION remove_watchlist_auction_ended();


------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION delete_product_on_auction_delete()
RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM Product WHERE productId = OLD.productId;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_product_on_auction_delete
AFTER DELETE ON Auction
FOR EACH ROW
EXECUTE FUNCTION delete_product_on_auction_delete();

------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION handle_likedauction_delete()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS(SELECT watchId FROM LikedAuction WHERE watchId = OLD.watchId) THEN
        DELETE FROM Watchlist w
        WHERE w.watchId = OLD.watchId;
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_watchlist
AFTER DELETE ON LikedAuction
FOR EACH ROW
EXECUTE FUNCTION handle_likedauction_delete();

------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION create_message_auction_ended()
RETURNS TRIGGER AS $$
DECLARE
    authuser1Id INT;
    authuser2Id INT;
BEGIN
    IF NEW.closeDate <= NOW() THEN

        SELECT authId INTO authuser1Id FROM Product WHERE productId = NEW.productId;  
        SELECT authId INTO authuser2Id FROM Bid WHERE auctionId = NEW.auctionId ORDER BY amount DESC LIMIT 1;

        -- Check if there is a bidder
        IF authuser2Id IS NOT NULL THEN
            -- Insert message to the auction owner
            INSERT INTO Messages(content, sentDate, senderId, auctionId) 
            VALUES ('Congratulations, you just won my auction!', NOW(), authuser1Id, NEW.auctionId);

            -- Insert message to the highest bidder
            INSERT INTO Messages(content, sentDate, senderId, auctionId) 
            VALUES ('Thank you very much, how should we proceed?', NOW(), authuser2Id, NEW.auctionId);
        END IF;
    END IF;


    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER create_message_auction_ended
AFTER UPDATE OF closeDate ON Auction
FOR EACH ROW
WHEN(NEW.closeDate <= NOW())
EXECUTE FUNCTION create_message_auction_ended();

------------------------------------------------------------------------------------------------------------


CREATE OR REPLACE FUNCTION premium_sub_ended()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
    VALUES (
        'Your premium subscription ended',
        'Message',
        NOW(),
        FALSE,
        OLD.authId,
        NULL
    );

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER premium_sub_ended
AFTER DELETE ON Premium
FOR EACH ROW
EXECUTE FUNCTION premium_sub_ended();

------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION send_notification_message_received()
RETURNS TRIGGER AS $$
DECLARE
    senderUsername TEXT;
    receiverId INT;
    auctionOwner INT;
BEGIN
    -- Retrieve the username of the sender
    SELECT username INTO senderUsername 
    FROM Users u
    JOIN AuthenticatedUser a ON a.uid = u.userid
    WHERE a.authid = NEW.senderId;

    -- Retrive auction owner
    SELECT p.authid INTO auctionOwner
    FROM Auction a
    JOIN Product p ON a.productid = p.productid
    WHERE a.auctionid = NEW.auctionId;

    -- Retrive the receiverId
    IF NEW.senderId != auctionOwner THEN
        receiverId := auctionOwner; -- Set receiverId to the auction owner if sender is not the owner
    ELSE
        -- Placeholder query to retrieve the user with the highest bid
        SELECT authId INTO receiverId
        FROM Bid
        WHERE auctionId = NEW.auctionId
        ORDER BY amount DESC
        LIMIT 1;
    END IF;

    -- Insert the notification with the sender's username
    INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
    VALUES (
        'New message from ' || senderUsername,
        'Message',
        NOW(),
        FALSE,
        receiverId,
        NULL
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER send_notification_message_received
AFTER INSERT ON Messages
FOR EACH ROW
EXECUTE FUNCTION send_notification_message_received();


------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION send_notification_new_review()
RETURNS TRIGGER AS $$
DECLARE
    auctionOwner INT;
BEGIN
    -- Retrieve the auction owner
    SELECT p.authid INTO auctionOwner
    FROM Auction a
    JOIN Product p ON a.productid = p.productid
    WHERE a.auctionid = NEW.auctionId;

    -- Insert a notification for the auction owner
    INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId)
    VALUES (
        'You received a new review.',
        'Review',
        NOW(),
        FALSE,
        auctionOwner,
        NULL
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER review_insert_notification
AFTER INSERT ON Review
FOR EACH ROW
EXECUTE FUNCTION send_notification_new_review();


------------------------------------------------------------------------------------------------------------



SET search_path TO lbaw2476;
-- category
INSERT INTO Category (categoryName) VALUES ('Electronics');
INSERT INTO Category (categoryName) VALUES ('Furniture');
INSERT INTO Category (categoryName) VALUES ('Collectibles');
INSERT INTO Category (categoryName) VALUES ('Clothing');
INSERT INTO Category (categoryName) VALUES ('Toys & Games');
INSERT INTO Category (categoryName) VALUES ('Jewelry');
INSERT INTO Category (categoryName) VALUES ('Sports Equipment');
INSERT INTO Category (categoryName) VALUES ('Books');
INSERT INTO Category (categoryName) VALUES ('Art');
INSERT INTO Category (categoryName) VALUES ('Automobiles');



-- subcategory
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Smartphones', 1);  -- Electronics
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Laptops', 1);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Tablets', 1);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Sofas', 2);  -- Furniture
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Chairs', 2);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Dining Sets', 2);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Vintage', 3);  -- Collectibles
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Coins', 3);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Action Figures', 3);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('T-Shirts', 4);  -- Clothing
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Jackets', 4);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Pants', 4);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Board Games', 5);  -- Toys & Games
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Action Figures', 5);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Stuffed Animals', 5);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Rings', 6);  -- Jewelry
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Necklaces', 6);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Bracelets', 6);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Bikes', 7);  -- Sports Equipment
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Fitness Equipment', 7);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Sports Memorabilia', 7);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Fiction', 8);  -- Books
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Non-Fiction', 8);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Children Books', 8);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Paintings', 9);  -- Art
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Sculptures', 9);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Prints', 9);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Cars', 10);  -- Automobiles
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Motorcycles', 10);
INSERT INTO SubCategory (subcategoryName, catId) VALUES ('Parts & Accessories', 10);



-- Users
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('anonymous', 'anonymous@example.com', '1234', '000000000');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('annebarnes', 'amandaparker@example.com', '9b06f0949220a0c1aeeaf8e7ac0bd06be8d3eecc4a07a4f99fce1988e0a82822', '(333)710-1612');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('agoodwin', 'richardsanders@example.net', 'ce3e536d0daa895641d79001c4e082eef77c5c4381613ba6e4e30d213872679f', '+1-302-352-6668');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('yrios', 'ubecker@example.com', 'f59c3271a989c93ea17ab33d07891000308f5f2b895e10b2d3e480b6c5a5116e', '743-735-3624x10');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('deniseshepherd', 'gina25@example.net', 'b4ed9093b1f33d5bb0baab3a50ec289fc1428b91eee636136df4b373c2869d4a', '744.692.8380');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('michaelmartin', 'heatherandrade@example.net', '45585827492c2c42f898984e6e1593e464ea4b93ce39199f38f1e195a80cc5dd', '001-893-507-730');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('andrew84', 'mistyjohnson@example.net', '651408c93c85cb34c1d61315d7c9f0e10264ecb30de00a82977d64cb0d0b1436', '(980)321-8961x3');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('wdiaz', 'rhondawong@example.org', 'c4a0aa694aa1f64d7afb3e3807ea58dfd789bbc021292fc364e0e6c9893eeafd', '450-672-6529x10');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('mvargas', 'angelareyes@example.org', '57a45aa6916489b47ace6cbdb2367c08fd672ab11ac3436ac1198e2e38073fc2', '294-773-3980x52');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('leenicholas', 'joneserica@example.net', '82a8db3c7f4c2be363a003b0623c86ed033e1d0d484edc69976a507e18a836f4', '330.679.0828');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('kayla71', 'vickibryant@example.net', 'fd5bb0e9f4e4a388ba294bf542b7f0da0cd2708b1ef14711c8d3bef57e964681', '309-804-1162x08');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('vgomez', 'anixon@example.org', '68f574d88927808c4359b5ec64859c8fb2fbb2183dd4366fdd49656fccc9cbf8', '(525)595-8828x7');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('johnwells', 'hughespamela@example.org', '6273f30a4af927806f16c4b19c7cef329021ae9b1f7f79cc8e0a8547ff682767', '712-302-2670x20');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('michelleyoung', 'mason85@example.com', '9d44cbbebd0464a7143a9a74bf33bf2e10f7f43eb6ef1541a7c145cb935c9507', '001-559-350-612');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('johnsonkatrina', 'achavez@example.org', '24e9e604c5b962885975f75734a361ed45f13dbf7c187109c5c6abdcb141e896', '(612)694-2934x6');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('amyhodge', 'randycrawford@example.com', '53e6fdde623dedea4368d480cf7d62801251dd561b1953162e6964e37250a5b2', '+1-655-398-3482');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('nichole30', 'julie05@example.org', '013b2b6ca78579fe3a0c7ad2984e31fbc39ee20309071ad2ff221c2f2dc6bd50', '+1-597-327-7016');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('jonesmatthew', 'derekwhitney@example.net', 'f3998135a72b7de7edb9a758dd3f313c8e52c377b0f180299e5129da5f634c6d', '703-770-6349x88');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('curtis09', 'xcarter@example.net', '2cbe97e9a51406d76b8f00a1e537e26f06b3b9da57a1258c8cedbabf7ed1d64b', '738.836.8758x10');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('janetkelly', 'victorporter@example.net', 'ebc7b07cfb1abed259dde324b38b93f45dc9c69f23f5a33f5c1db389e09d356c', '+1-756-703-2089');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('hartmichael', 'foxamy@example.com', 'ad9aa6cbfc656eff5f1751195d577cdb822b2d5dce6ef5009d42656d3151d605', '(627)854-0277x6');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('professor', 'professor@example.com', '$2y$10$jYKOkG9Qk9a6oNwWZ0us8OZ3Y75KWbYpcFlyCDHQ4w/JotOZWbvqa', '932079590');
INSERT INTO Users (username, email, password, phoneNumber) VALUES ('professorAdmin', 'professoradmin@example.com', '$2y$10$jYKOkG9Qk9a6oNwWZ0us8OZ3Y75KWbYpcFlyCDHQ4w/JotOZWbvqa', '912916260');  



-- Admin
INSERT INTO Admin (adminLevel, uId) VALUES (5, 18);
INSERT INTO Admin (adminLevel, uId) VALUES (5, 17);
INSERT INTO Admin (adminLevel, uId) VALUES (4, 12);
INSERT INTO Admin (adminLevel, uId) VALUES (5, 5);
INSERT INTO Admin (adminLevel, uId) VALUES (3, 11);
INSERT INTO Admin (adminLevel, uId) VALUES (5, 23);



-- Auth
INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('123 Main St, Anytown, USA', NOW(), 1, 100.00); -- ID 1

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('456 Oak St, Springfield, IL', NOW(), 2, 200.50); -- ID 2

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('789 Pine St, Denver, CO', NOW(), 3, 150.75); -- ID 3

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('101 Maple St, Chicago, IL', NOW(), 4, 120.00); -- ID 4

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('202 Birch St, Houston, TX', NOW(), 5, 90.25); -- ID 5

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('303 Cedar St, Phoenix, AZ', NOW(), 6, 180.00); -- ID 6

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('404 Elm St, San Francisco, CA', NOW(), 7, 160.00); -- ID 7

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('505 Birchwood St, Dallas, TX', NOW(), 8, 130.00); -- ID 8

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('606 Redwood St, Los Angeles, CA', NOW(), 9, 140.00); -- ID 9

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('707 Palm St, Miami, FL', NOW(), 10, 110.00); -- ID 10

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('808 Oakwood St, Seattle, WA', NOW(), 11, 250.00); -- ID 11

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('909 Fir St, Orlando, FL', NOW(), 12, 80.00); -- ID 12

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1010 Pinewood St, New York, NY', NOW(), 13, 70.00); -- ID 13

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1111 Maplewood St, Boston, MA', NOW(), 14, 50.00); -- ID 14

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1212 Willow St, Austin, TX', NOW(), 15, 90.50); -- ID 15

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1313 Cedarwood St, Raleigh, NC', NOW(), 16, 200.00); -- ID 16

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1414 Birchwood St, Denver, CO', NOW(), 17, 110.75); -- ID 17

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('1515 Redwood St, San Diego, CA', NOW(), 18, 190.00); -- ID 18

INSERT INTO AuthenticatedUser (address, registerDate, uId, balance)
VALUES ('Rua Adelino da Costa Campos', NOW(), 22, 16000.00); -- ID 22




-- Premium
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-10-02', 2);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-04-16', 8);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-07-12', 3);
INSERT INTO Premium (expiryDate, authId) VALUES ('2024-12-24', 7);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-07-04', 10);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-09-05', 9);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-04-02', 1);
INSERT INTO Premium (expiryDate, authId) VALUES ('2025-06-30', 19);



-- Product
INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Vintage Lamp', 'A beautiful vintage lamp with intricate designs.', 'Good', 3, 1);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Gaming Laptop', 'High-performance laptop suitable for gaming and heavy tasks.', 'Good', 2, 2);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Coffee Maker', 'Automatic coffee maker with multiple settings.', 'Good', 17, 3);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Antique Vase', 'Rare porcelain vase with historical significance.', 'Good', 4, 4);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Mountain Bike', 'A durable mountain bike perfect for off-road trails.', 'Good', 19, 8);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Road Bike', 'A durable road bike perfect for road travels', 'Very Good', 17, 8);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Smartphone', 'Latest model smartphone with cutting-edge features.', 'Like New', 6, 2);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Leather Sofa', 'Comfortable leather sofa in excellent condition.', 'Brand New', 5, 5);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Painting by Local Artist', 'Beautiful landscape painting by a renowned local artist.', 'Like New', 7, 6);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Electric Guitar', 'Fully functional electric guitar with a sleek design.', 'Good', 8, 7);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Dining Table Set', 'Wooden dining table with six matching chairs.', 'Very Good', 9, 5);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Fitness Tracker', 'Track your steps, heart rate, and calories burned.', 'Brand New', 10, 9);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Vintage Record Player', 'Classic record player with excellent sound quality.', 'Very Good', 17, 10);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Camping Tent', 'Spacious tent suitable for family camping trips.', 'Good', 12, 8);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Jewelry Box', 'Elegant wooden jewelry box with multiple compartments.', 'Acceptable', 13, 4);

INSERT INTO Product (title, description, state, authId, subCatId) 
VALUES ('Drone with Camera', 'High-quality drone equipped with a 4K camera.', 'Very Good', 14, 7);


-- Auction
INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2025-11-5 16:34:40', 150.00,1);

INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2025-12-5 13:34:40', 1200.00,2);

INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2025-12-5 13:34:40', 85.50,3);

INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2025-12-5 13:34:40', 3000.00,4);

INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2024-12-5 13:34:40', 450.00,5);

INSERT INTO Auction (initDate, closeDate, initValue,productId) 
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 600.00,6);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 200.00,7);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 100.00,8);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 350.00,9);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 500.00,10);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 250.00,11);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 150.00,12);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 400.00,13);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 175.00,14);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 275.00,15);

INSERT INTO Auction (initDate, closeDate, initValue,productId)
VALUES ('2024-11-5 13:57:30', '2025-12-5 14:40:40', 225.00,16);



-- Watchlist
INSERT INTO Watchlist (authId) 
VALUES (1);

INSERT INTO Watchlist (authId) 
VALUES (2);

INSERT INTO Watchlist (authId) 
VALUES (3);

INSERT INTO Watchlist (authId) 
VALUES (4);

INSERT INTO Watchlist (authId) 
VALUES (5);



-- LikedAuction
INSERT INTO LikedAuction (watchId, auctionId) 
VALUES (1, 1);

INSERT INTO LikedAuction (watchId, auctionId) 
VALUES (2, 1);

INSERT INTO LikedAuction (watchId, auctionId) 
VALUES (3, 2);

INSERT INTO LikedAuction (watchId, auctionId) 
VALUES (4, 3);

INSERT INTO LikedAuction (watchId, auctionId) 
VALUES (5, 4);



-- Bid
INSERT INTO Bid (amount, bidDate, authId, auctionId) 
VALUES (250.00, '2024-10-01 11:00:00', 4, 1);

INSERT INTO Bid (amount, bidDate, authId, auctionId) 
VALUES (500.00, '2024-10-02 15:29:00', 5, 1);

INSERT INTO Bid (amount, bidDate, authId, auctionId) 
VALUES (7500.00, '2024-10-05 10:45:00', 3, 2);

INSERT INTO Bid (amount, bidDate, authId, auctionId) 
VALUES (900.00, '2024-10-07 09:00:00', 1, 3);

INSERT INTO Bid (amount, bidDate, authId, auctionId) 
VALUES (3200.00, '2024-10-08 13:00:00', 19, 4);

INSERT INTO Bid (amount, bidDate, authId, auctionId)
VALUES (500.00, '2024-10-08 13:00:00', 4, 5);



-- Notification
INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId) 
VALUES ('New bid placed on your auction', 'Auction End', '2024-10-01 12:00:00', FALSE, 1, 1);

INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId) 
VALUES ('Auction ending soon', 'Auction End', '2024-10-02 08:30:00', FALSE, 2, 1);

INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId) 
VALUES ('You won the auction', 'Auction End', '2024-10-05 17:00:00', TRUE, 3, 2);

INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId) 
VALUES ('New bid on item you are watching', 'Auction End', '2024-10-07 12:15:00', FALSE, 4, 3);

INSERT INTO Notification (content, type, sentDate, seen, authId, auctionId) 
VALUES ('Auction closed', 'Auction End', '2024-10-09 18:30:00', TRUE, 5, 4);



-- Image
INSERT INTO Image (image, productId) 
VALUES ('images/storage/auctions/1-1.png', 1);

INSERT INTO Image (image, productId) 
VALUES ('images/storage/auctions/2-1.png', 2);

INSERT INTO Image (image, productId) 
VALUES ('images/storage/auctions/3-1.png', 3);

INSERT INTO Image (image, productId) 
VALUES ('images/storage/auctions/4-1.png', 4);

INSERT INTO Image (image, productId) 
VALUES ('images/storage/auctions/5-1.png', 5);



-- Review
INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
VALUES ('Great auction experience!', 4.50, '2024-10-10 10:00:00', 1, 1);

INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
VALUES ('Product was as described, very satisfied.', 5.00, '2024-10-12 14:30:00', 5, 2);

INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
VALUES ('Could improve packaging, but overall okay.', 3.75, '2024-10-15 16:00:00', 3, 3);

INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
VALUES ('Excellent communication and fast shipping!', 4.90, '2024-10-18 11:45:00', 4, 4);

INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
VALUES ('Item was not as described. Disappointed.', 2.00, '2024-10-20 19:30:00', 5, 5);



-- Messages
INSERT INTO Messages (content, sentDate, senderId, auctionId) 
VALUES ('Is the item still available?', '2024-10-01 09:15:00', 4, 5);

INSERT INTO Messages (content, sentDate, senderId, auctionId) 
VALUES ('What is the current highest bid?', '2024-10-02 13:30:00', 4, 5);

INSERT INTO Messages (content, sentDate, senderId, auctionId) 
VALUES ('Can you provide more pictures of the item?', '2024-10-03 17:20:00', 4, 5);

INSERT INTO Messages (content, sentDate, senderId, auctionId) 
VALUES ('When does the auction end?', '2024-10-05 11:45:00', 4, 5);

INSERT INTO Messages (content, sentDate, senderId, auctionId) 
VALUES ('Thank you for the quick response!', '2024-10-06 14:10:00', 4, 5);
