//
//  OoIMeta.m
//  WordPress
//
//  Created by Shakir Ali on 18/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "OoIMeta.h"

@implementation OoIMeta

@synthesize artist;
@synthesize artistdate;
@synthesize artworkdate;
@synthesize imageurl;
@synthesize reference;
@synthesize title;

-(void)dealloc{
    [artist release];
    [artistdate release];
    [artworkdate release];
    [imageurl release];
    [reference release];
    [title release];
    [super dealloc];
}

@end
