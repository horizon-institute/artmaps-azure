//
//  MetaListTableViewController.h
//  WordPress
//
//  Created by Shakir Ali on 04/08/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "OoIMetaLoader.h"

@class PostMapLocation;

@interface MetaListTableViewController : UITableViewController <OoIMetaLoaderDelegate>
@property (nonatomic, retain) NSArray *ooI_IDs;
@property (nonatomic, retain) PostMapLocation *postMapLocation;

@end
