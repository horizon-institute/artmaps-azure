//
//  BlogExperience.m
//  WordPress
//
//  Created by Shakir Ali on 19/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "BlogExperience.h"

@implementation BlogExperience

@synthesize name;
@synthesize context;
@synthesize iconPath;

-(NSString*)getTitleForOoIList{
    return @"Paintings";
}

-(NSString*)getTitleForOoI{
    return @"Painting";
}

-(void)dealloc{
    [name release];
    [context release];
    [iconPath release];
    [super dealloc];
}

@end
