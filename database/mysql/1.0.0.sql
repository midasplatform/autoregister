CREATE TABLE IF NOT EXISTS `autoregister_targetedcommunity` (
    `targetedcommunity_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `community_id` bigint(20) NOT NULL,
    `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`targetedcommunity_id`)
) DEFAULT CHARSET=utf8;
