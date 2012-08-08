//
//  MetaTableViewController.h
//  WordPress
//
//  Created by Shakir Ali on 26/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "OoIMeta.h"

@class PostMapLocation;

@interface MetaTableViewController : UITableViewController

typedef enum{
    kArtist = 0,
    kArtistDate,
    kArtworkDate,
    kImageUrl,
    kReference,
    kTitle,
    NO_OF_SECTIONS
} metasections;

@property (nonatomic, retain) OoIMeta *ooiMeta;
@property (nonatomic, retain) PostMapLocation *postMapLocation;

@end