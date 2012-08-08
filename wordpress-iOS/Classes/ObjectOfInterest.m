//
//  ObjectOfInterest.m
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "ObjectOfInterest.h"

@implementation ObjectOfInterest

@synthesize OoI_id;
@synthesize uri;
@synthesize ooiLocations;
@synthesize ooiActions;
@synthesize meta;

-(void)dealloc{
    [uri release];
    [ooiLocations release];
    [ooiActions release];
    [meta release];
    [super dealloc];
}

@end
