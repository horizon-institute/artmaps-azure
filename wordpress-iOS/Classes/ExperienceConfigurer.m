//
//  ExperienceConfigurer.m
//  WordPress
//
//  Created by Shakir Ali on 19/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "ExperienceConfigurer.h"
#import "Blog.h"

@implementation ExperienceConfigurer

@synthesize currentExperience;
@synthesize selectedBlog;

static ExperienceConfigurer *sharedInstance = nil;

+(ExperienceConfigurer*)sharedInstance{
    if (sharedInstance){
        return sharedInstance;
    }
    @synchronized(self)
    {
        if (sharedInstance == nil ){
            sharedInstance = [[ExperienceConfigurer alloc] init];
            sharedInstance.currentExperience = [[sharedInstance getRegisteredBlogExperiences] objectAtIndex:0];
        }
    }
    return sharedInstance;
}

-(void)dealloc{
    [currentExperience release];
    [selectedBlog release];
    [sharedInstance release];
    [super dealloc];
}

+(id)allocWithZone:(NSZone*)zone
{
	@synchronized(self) {
        if (sharedInstance == nil) {
            sharedInstance = [super allocWithZone:zone];
            return sharedInstance;  // assignment and return on first allocation
        }
    }
    return nil; // on subsequent allocation attempts return nil 
}

-(id)copyWithZone:(NSZone*)zone
{
	return self;
}

-(id)retain{
	return self;
}

-(NSUInteger)retainCount
{
	return NSUIntegerMax;
}

-(id)autorelease
{
	return self;
}

#pragma mark instance functions.
-(NSArray*)getRegisteredBlogExperiences{
    NSMutableArray *experiences = [[NSMutableArray alloc] initWithCapacity:0];
    BlogExperience *blogExperience = [[BlogExperience alloc] init];
    blogExperience.name = @"Tate";
    blogExperience.context=@"tate";
    blogExperience.iconPath=@"tate.png";
    [experiences addObject:blogExperience];
    [blogExperience release];
    return [experiences autorelease];
}


@end
